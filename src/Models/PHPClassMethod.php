<?php

namespace Drewlabs\CodeGenerator\Models;

use Drewlabs\CodeGenerator\CommentModelFactory;
use Drewlabs\CodeGenerator\Contracts\ClassMethodInterface;
use Drewlabs\CodeGenerator\Contracts\FunctionParameterInterface;
use Drewlabs\CodeGenerator\Models\Traits\HasClassMemberDefinitions;
use Drewlabs\CodeGenerator\Models\Traits\HasImportDeclarations;
use Drewlabs\CodeGenerator\Models\Traits\HasIndentation;
use Drewlabs\Core\Helpers\Arrays\BinarySearchResult;

/** @package Drewlabs\CodeGenerator\Models */
class PHPClassMethod implements ClassMethodInterface
{

    use HasImportDeclarations;
    use HasIndentation;
    use HasClassMemberDefinitions;

    /**
     * @var string
     */
    private $name_;
    /**
     * @var FunctionParameterInterface[]
     */
    private $params_;

    /**
     * PHP Stringeable component
     * 
     * @var mixed
     */
    private $comment_;

    /**
     * The returns type of the function
     *
     * @var string|array
     */
    private $returns_;

    /**
     *
     * @var string
     */
    private $exceptions_;

    /**
     * Indicates whether the method is static or not
     *
     * @var bool
     */
    private $isStatic_;

    /**
     * Method defintion content
     *
     * @var string
     */
    private $content_;

    /**
     * Indicates whether the definition is return as interface method or a class method
     *
     * @var bool
     */
    private $isInterfaceMethod_ = false;

    public function __construct(
        string $name,
        array $params = [],
        string $returns = null,
        $modifier = 'public',
        $descriptors = ''
    ) {
        $this->name_ = $name;
        // Add list of params to the method
        foreach (array_filter($params ?? []) as $value) {
            $this->addParam($value);
        }
        if (null !== $descriptors) {
            $this->addComment($descriptors);
        }
        if (null !== $modifier) {
            $this->setModifier($modifier);
        }
        $this->returns_ = $returns;
    }

    public function throws($exceptions = [])
    {
        if (null !== $exceptions) {
            $exceptions = drewlabs_core_strings_is_str($exceptions) ? [$exceptions] : (is_array($exceptions) ? $exceptions : []);
            foreach ($exceptions as $value) {
                if (drewlabs_core_strings_contains($value, '\\')) {
                    $this->imports_[] = $value;
                    $this->exceptions_[] = drewlabs_core_strings_after_last('\\', $value);
                } else {
                    $this->exceptions_[] = $value;
                }
            }
        }
        return $this;
    }

    /**
     * Add a new Parameter to the method
     *
     * @return self
     */
    public function addParam(FunctionParameterInterface $param)
    {
        #region Validate method parameters for duplicated entries
        $params = [];
        foreach (($this->params_ ?? []) as $value) {
            $params[$value->name()] = $value;
        }
        sort($params);
        $match = drewlabs_core_array_bsearch(array_keys($params), $param, function($curr,  FunctionParameterInterface $item) use ($params) {
            if ($params[$curr]->equals($item)) {
                return BinarySearchResult::FOUND;
            }
            return strcmp($curr, $item->name()) > 0 ? BinarySearchResult::LEFT : BinarySearchResult::RIGHT;
        });
        if ($match !== BinarySearchResult::LEFT) {
            throw new \RuntimeException(sprintf('Duplicated entry %s in method %s definition : ', $param->name(), $this->name_));
        }
        #endregion Validate method parameters for duplicated entries
        $this->params_[] = $param;
        return $this;
    }

    public function asStatic(bool $value)
    {
        $this->isStatic_ = $this->name_ === '__construct' ? false : ($value || false);
        return $this;
    }

    public function addContents(string $content)
    {
        $this->content_ = $content;
        return $this;
    }

    public function asInterfaceMethod()
    {
        $this->isInterfaceMethod_ = true;
        return $this;
    }

    public function equals(ClassMethodInterface $value)
    {
        return $this->name_ === $value->getName();
        // If PHP Support method overloading go deep to method definitions
    }


    protected function setFileImports()
    {
        if (null !== $this->returns_) {
            if (drewlabs_core_strings_contains($this->returns_, '\\')) {
                $this->returns_ = $this->addClassPathToImportsPropertyAfter(function ($classPath) {
                    return $this->getClassFromClassPath($classPath);
                })($this->returns_);
            }
        }
        $values = [];
        if ((null !== $this->params_)) {
            $values = is_array($this->params_) ? $this->params_ : (is_string($this->params_) ? [$this->params_] : []);
            $params = [];
            foreach ($values as $value) {
                if (drewlabs_core_strings_contains($value->type(), '\\')) {
                    $params[] = new PHPFunctionParameter(
                        $value->name(),
                        // drewlabs_core_strings_after_last('\\', $value->type()),
                        $this->addClassPathToImportsPropertyAfter(function ($classPath) {
                            return $this->getClassFromClassPath($classPath);
                        })($value->type()),
                        $value->defaultValue()
                    );
                } else {
                    $params[] = $value;
                }
            }
            $this->params_ = $params;
        }
        return $this;
    }

    protected function setComments()
    {
        $descriptors = array_filter((drewlabs_core_strings_is_str($this->descriptors_) ? [$this->descriptors_] : $this->descriptors_) ?? []);
        if (!empty($descriptors)) {
            // Add a line separator between the descriptors and other definitions
            $descriptors[] = "";
        }
        // Generates method params comments
        if (null !== $this->params_) {
            foreach ($this->params_ as $value) {
                $type = null === $value->type() ? "mixed" : $value->type();
                $descriptors[] = "@param " . $type . " " . $value->name();
            }
        }
        // Generate exception comment
        if ((null !== $this->exceptions_) && is_array($this->exceptions_)) {
            foreach ($this->exceptions_ as $value) {
                $descriptors[] = "@throws $value";
            }
        }
        // Generate returns comment
        if (null !== $this->returns_) {
            $descriptors[] = "@return " . $this->returns_;
        }
        $this->comment_ = (new CommentModelFactory(true))->make($descriptors);
        return $this;
    }

    public function __toString(): string
    {
        $this->setFileImports()->setComments();
        if ($this->getIndentation()) {
            $parts[] = $this->comment_->setIndentation($this->getIndentation())->__toString();
        } else {
            $parts[] = $this->comment_->__toString();
        }
        $accessModifier = (null !== $this->accessModifier_) && in_array(
            $this->accessModifier_,
            [
                'private', 'protected', 'public'
            ]
        ) && !$this->isInterfaceMethod_ ? $this->accessModifier_ : "public";
        // Start the declaration
        $declaration = $this->isStatic_ ? "$accessModifier static function $this->name_(" : "$accessModifier function $this->name_(";
        // Add method params
        if (null !== $this->params_) {
            $params = array_map(function ($param) {
                $type = null === $param->type() ? "" : $param->type();
                $result = "$type \$" . $param->name();
                return null === $param->defaultValue() ? $result : "$result = " . drewlabs_core_strings_replace('"null"', 'null', $param->defaultValue());
            }, array_merge(
                array_filter($this->params_, function($p) {
                    return !$p->isOptional();
                }),
                array_filter($this->params_, function($p) {
                    return $p->isOptional();
                })
            ));
            $declaration .= implode(", ", $params);
        }
        // Add the closing parenthesis
        $declaration .=  ")";
        // If it is an interface method, close the definition
        if ($this->isInterfaceMethod_) {
            $parts[] = "$declaration;";
        } else {
            // If it is not an interface method, add the method body
            $parts[] = $declaration;
            $parts[] = "{";
                $parts[] = "\t# code...";
                if (null !== $this->content_) {
                    $splitted_contents = explode(PHP_EOL, $this->content_);
                    $splitted_contents = array_map(function ($content) {
                        return "\t$content";
                    }, $splitted_contents);
                    $parts[] = implode(PHP_EOL, $splitted_contents);
                }
                $parts[] = "}";
        }
        if ($this->getIndentation()) {
            $parts = array_map(function ($part) {
                return $this->getIndentation() . "$part";
            }, $parts);
        }
        return implode(PHP_EOL, $parts);
    }
}
