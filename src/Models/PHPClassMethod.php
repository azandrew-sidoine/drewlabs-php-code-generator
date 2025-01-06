<?php

declare(strict_types=1);

/*
 * This file is part of the Drewlabs package.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\CodeGenerator\Models;

use Drewlabs\CodeGenerator\CommentModelFactory;
use Drewlabs\CodeGenerator\Contracts\CallableInterface;
use Drewlabs\CodeGenerator\Contracts\ClassMemberInterface;
use Drewlabs\CodeGenerator\Contracts\FunctionParameterInterface;
use Drewlabs\CodeGenerator\Contracts\HasVisibility;
use Drewlabs\CodeGenerator\Helpers\Arr;
use Drewlabs\CodeGenerator\Helpers\PHPLanguageDefifinitions;
use Drewlabs\CodeGenerator\Helpers\Str;
use Drewlabs\CodeGenerator\Models\Traits\BelongsToNamespace;
use Drewlabs\CodeGenerator\Models\Traits\HasImportDeclarations;
use Drewlabs\CodeGenerator\Models\Traits\HasIndentation;
use Drewlabs\CodeGenerator\Models\Traits\HasPHP8Attributes;
use Drewlabs\CodeGenerator\Models\Traits\OOPStructComponentMembers;
use Drewlabs\CodeGenerator\Models\Traits\Type;
use Drewlabs\CodeGenerator\Types\PHPTypesModifiers;
use Drewlabs\CodeGenerator\Contracts\HasPHP8Attributes as AbstractHasPHP8Attributes;

class PHPClassMethod implements CallableInterface, ClassMemberInterface, AbstractHasPHP8Attributes
{
    use BelongsToNamespace;
    use HasImportDeclarations;
    use HasIndentation;
    use OOPStructComponentMembers;
    use Type;
    use HasPHP8Attributes;

    /**
     * @var FunctionParameterInterface[]
     */
    private $params;

    /**
     * PHP Stringeable component.
     *
     * @var mixed
     */
    private $comment;

    /**
     * The returns type of the function.
     *
     * @var string|array
     */
    private $returns;

    /**
     * @var string
     */
    private $exceptions;

    /**
     * Indicates whether the method is static or not.
     *
     * @var bool
     */
    private $isStatic;

    /**
     * Method defintion content.
     *
     * @var string[]
     */
    private $contents;

    /**
     * Indicates whether the definition is return as interface method or a class method.
     *
     * @var bool
     */
    private $isInterfaceMethod = false;

    public function __construct(
        string $name,
        array $params = [],
        ?string $returns = null,
        ?string $modifier = 'public',
        $descriptors = ''
    ) {
        $this->setName($name);
        // Add list of params to the method
        foreach (
            array_filter($params ?? [], static function ($value) {
                return $value instanceof FunctionParameterInterface;
            }) as $value
        ) {
            $this->addParameter($value);
        }
        if (null !== $descriptors) {
            $this->addComment($descriptors);
        }
        if (null !== $modifier) {
            $this->setModifier($modifier);
        }
        if ($returns) {
            $this->setReturnType($returns);
        }
    }

    private function orderParameters()
    {
        $params_ = $this->getParameters();
        $this->params = array_merge(
            array_filter($params_, static function ($p) {
                return !$p->isOptional() && !$p->isVariadic();
            }),
            array_filter($params_, static function ($p) {
                return $p->isOptional() && !$p->isVariadic();
            }),
            array_filter($params_, static function ($p) {
                return $p->isVariadic();
            })
        );
        return $this;
    }

    public function __toString(): string
    {
        $this->orderParameters()->prepare()->setComments();
        $name = $this->getName();

        $accessModifier = (null !== $this->accessModifier) &&
            \in_array($this->accessModifier, [PHPTypesModifiers::PRIVATE, PHPTypesModifiers::PROTECTED, PHPTypesModifiers::PUBLIC], true)
            && !$this->isInterfaceMethod ?
            $this->accessModifier :
            PHPTypesModifiers::PUBLIC;

        // Start the declaration
        $declaration = $this->isStatic ? "$accessModifier static function $name(" : "$accessModifier function $name(";
        // Add method params
        if (null !== ($params = $this->getParameters())) {
            $params = array_map(static function ($param) {
                // Add the type definition
                $type = $param->type();
                // Add the visibility case param is an instance of HasVisibility
                $definitions[] = $type ? sprintf('%s%s ', ($param instanceof HasVisibility && (version_compare(\PHP_VERSION, '8.0.0') >= 0)) ? sprintf('%s ', $param->getVisibility()) : '', $type) : (($param instanceof HasVisibility && (version_compare(\PHP_VERSION, '8.0.0') >= 0)) ? sprintf('%s ', $param->getVisibility()) : null);
                // Add the reference definition
                $definitions[] = $param->isReference() ? '&' : null;
                // Add the variadic definition
                $definitions[] = $param->isVariadic() ? '...' : null;
                // Add the name definition
                $definitions[] = sprintf('$%s', $param->name());
                // Filter out null values
                $definitions = array_filter($definitions);
                // Generate the defintion
                $result = implode('', $definitions);
                return ((null === $param->defaultValue()) || $param->isVariadic()) ?
                    $result : "$result = " . str_replace('"null"', 'null', $param->defaultValue());
            }, $params);
            $declaration .= implode(', ', $params);
        }
        // Add the closing parenthesis
        $declaration .= ')';

        // Case running a PHP version >= 7.2, we add the return type of the function to the function declaration
        if (version_compare(\PHP_VERSION, '7.2.0') >= 0 && $this->returns && strtolower($this->returns) != 'mixed') {
            $declaration .= sprintf(": %s", $this->returns);
        }

        $indentation = $this->getIndentation();
        $parts[] = null !== $indentation ?
            $this->comment->setIndentation($this->getIndentation())->__toString() :
            $this->comment->__toString();
        
        // Add PHP8 attributes to method/function definition
        foreach ($this->getAttributes() as $attribute) {
            $parts[] = sprintf("%s", PHP8Attribute::new($attribute)->__toString());
        }
        // If it is an interface method, close the definition
        if ($this->isInterfaceMethod) {
            $parts[] = "$declaration;";
        } else {
            // If it is not an interface method, add the method body
            $parts[] = $declaration;
            $parts[] = '{';
            if (!empty(($contents = array_merge(["\t# code..."], $this->contents ?? [])))) {
                $counter = 0;
                $parts[] = implode(\PHP_EOL, array_map(static function ($content) use ($indentation, &$counter) {
                    $content = $indentation && $counter > 0 ? $indentation . $content : $content;
                    ++$counter;

                    return $content;
                }, $contents));
            }
            $parts[] = '}';
        }
        if ($indentation) {
            $parts = array_map(static function ($part) use ($indentation) {
                return $indentation . "$part";
            }, $parts);
        }

        return implode(\PHP_EOL, $parts);
    }

    public function getParameters()
    {
        return $this->params ?? [];
    }

    public function throws($exceptions = [])
    {
        if (null !== $exceptions) {
            $exceptions = is_string($exceptions) ? [$exceptions] : (\is_array($exceptions) ? $exceptions : []);
            foreach ($exceptions as $value) {
                if (Str::contains($value, '\\')) {
                    $this->imports[] = $value;
                    $this->exceptions[] = Str::afterLast('\\', $value);
                } else {
                    $this->exceptions[] = $value;
                }
            }
        }

        return $this;
    }

    /**
     * Add a new Parameter to the method.
     *
     * @return self
     */
    public function addParameter(FunctionParameterInterface $param)
    {
        //region Validate method parameters for duplicated entries
        $params = [];
        foreach ($this->getParameters() as $value) {
            $params[$value->name()] = $value;
        }
        sort($params);
        $match = Arr::bsearch(array_keys($params), $param, static function ($curr, FunctionParameterInterface $item) use ($params) {
            if ($params[$curr]->equals($item)) {
                return 0;
            }

            return strcmp($params[$curr]->name(), $item->name()) > 0 ? -1 : 1;
        });
        if (-1 !== $match) {
            throw new \RuntimeException(sprintf('Duplicated entry %s in method %s definition : ', $param->name(), $this->getName()));
        }
        //endregion Validate method parameters for duplicated entries
        $this->params[] = $param;

        return $this;
    }

    public function asStatic(bool $value)
    {
        $this->isStatic = '__construct' === $this->getName() ? false : ($value || false);

        return $this;
    }

    public function addContents(string $contents)
    {
        $self = $this;
        if (null !== $contents) {
            $values = explode(\PHP_EOL, $contents);
            $values = array_map(static function ($content) {
                return rtrim("$content", ';');
            }, $values);
            foreach ($values as $value) {
                // code...
                $self = $self->addLine($value);
            }
        }

        return $self;
    }

    /**
     * {@inheritDoc}
     *
     * Note : Lines must not be terminated with extras ; because the implementation will add a trailing ; at the end
     */
    public function addLine(string $line)
    {
        // Checks if the line is an expression, a block or a comments
        if (
            empty($line) ||
            PHPLanguageDefifinitions::isComment($line) ||
            PHPLanguageDefifinitions::isBlock($line) ||
            PHPLanguageDefifinitions::startsWithSpecialCharacters($line) ||
            PHPLanguageDefifinitions::endsWithSpecialCharacters($line)
        ) {
            $this->contents[] = "\t$line";
        } else {
            $this->contents[] = "\t$line;";
        }

        return $this;
    }

    public function asCallableSignature()
    {
        $this->isInterfaceMethod = true;

        return $this;
    }

    public function equals(CallableInterface $value)
    {
        return $this->getName() === $value->getName();
        // If PHP Support method overloading go deep to method definitions
    }

    public function setReturnType(string $type)
    {
        $this->returns = $type;

        return $this;
    }

    protected function prepare()
    {
        if (null !== $this->returns) {
            if (Str::contains($this->returns, '\\')) {
                $this->returns = $this->addClassPathToImportsPropertyAfter(function ($classPath) {
                    return $this->getClassFromClassPath($classPath);
                })($this->returns);
            }
        }
        $values = [];
        $params_ = $this->getParameters();
        if (!empty($params_)) {
            $values = \is_array($params_) ? $params_ : (\is_string($params_) ? [$params_] : []);
            $params = [];
            foreach ($values as $value) {
                if (Str::contains($value->type(), '\\')) {
                    $params[] = new PHPFunctionParameter(
                        $value->name(),
                        $this->addClassPathToImportsPropertyAfter(function ($classPath) {
                            return $this->getClassFromClassPath($classPath);
                        })($value->type()),
                        $value->defaultValue()
                    );
                } else {
                    $params[] = $value;
                }
            }
            $this->params = $params;
        }

        return $this;
    }

    protected function setComments()
    {
        $descriptors = $this->comments();
        if (!empty($descriptors)) {
            // Add a line separator between the descriptors and other definitions
            $descriptors[] = '';
        }
        // Generates method params comments
        $params_ = $this->getParameters();
        if (null !== $params_) {
            foreach ($params_ as $value) {
                $type = null === $value->type() ? 'mixed' : $value->type();
                $descriptors[] = '@param ' . $type . ' $' . $value->name();
            }
        }
        // Generate exception comment
        if ((null !== $this->exceptions) && \is_array($this->exceptions)) {
            foreach ($this->exceptions as $value) {
                $descriptors[] = "@throws $value";
            }
        }
        // Generate returns comment
        if (null !== $this->returns) {
            $descriptors[] = '@return ' . $this->returns;
        }
        $this->comment = (new CommentModelFactory(true))->make($descriptors);

        return $this;
    }
}
