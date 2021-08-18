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
use Drewlabs\CodeGenerator\Contracts\ClassMemberInterface;
use Drewlabs\CodeGenerator\Contracts\ValueContainer;
use Drewlabs\CodeGenerator\Models\Traits\BelongsToNamespace;
use Drewlabs\CodeGenerator\Models\Traits\HasImportDeclarations;
use Drewlabs\CodeGenerator\Models\Traits\HasIndentation;
use Drewlabs\CodeGenerator\Models\Traits\OOPStructComponentMembers;
use Drewlabs\CodeGenerator\Types\PHPTypes;
use Drewlabs\CodeGenerator\Types\PHPTypesModifiers;

class PHPClassProperty implements ValueContainer, ClassMemberInterface
{
    use BelongsToNamespace;
    use HasImportDeclarations;
    use HasIndentation;
    use OOPStructComponentMembers;

    /**
     * @var string
     */
    private $type_;

    /**
     * List of imports to append to the file/class imports.
     *
     * @var string[]
     */
    private $imports_;

    /**
     * PHP Stringeable component.
     *
     * @var mixed
     */
    private $comment_;

    /**
     * The default value to set the property to.
     *
     * @var string|array
     */
    private $value_;

    /**
     * Indicates that the property is a constant property.
     *
     * @var bool
     */
    private $isConstant_ = false;

    /**
     * Class instances initializer.
     *
     * @param string       $modifier
     * @param string|array $default
     */
    public function __construct(
        string $name,
        ?string $type = null,
        $modifier = 'public',
        $default = null,
        $descriptors = ''
    ) {
        $this->name_ = $name;
        if (null !== $type) {
            $this->setType($type);
        }
        if (null !== $descriptors) {
            $this->addComment($descriptors);
        }
        if (null !== $modifier) {
            $this->setModifier($modifier);
        }
        $this->value($default ?? '');
    }

    public function __toString(): string
    {
        $this->setImports()
            ->setComments()
            ->value($this->value_);
        // Generate comments
        if ($this->getIndentation()) {
            $parts[] = $this->comment_->setIndentation($this->getIndentation())->__toString();
        } else {
            $parts[] = $this->comment_->__toString();
        }
        // Generate defintion / declarations
        $modifier = (null !== $this->accessModifier_) && \in_array(
            $this->accessModifier_,
            [
                'private', 'protected', 'public',
            ],
            true
        ) ? $this->accessModifier_ : PHPTypesModifiers::PUBLIC;
        $definition = $this->isConstant_ ? sprintf('%s %s %s', $modifier, PHPTypesModifiers::CONSTANT, $this->name_) : "$modifier \$$this->name_";
        // TODO : Review this part after all classes tested successfully
        $value = $this->parsePropertyValue();
        if (drewlabs_core_strings_contains($value, '"[') && drewlabs_core_strings_contains($value, ']"')) {
            $value = drewlabs_core_strings_replace(' ]"', ']', drewlabs_core_strings_replace('"[', '[', $value));
        }
        if ($value && \is_string($value) && !empty($value)) {
            $definition .= drewlabs_core_strings_replace('"null"', 'null', drewlabs_core_strings_replace(['""'], '"', " = $value;"));
        } else {
            $definition .= ';';
        }
        $parts[] = $definition;
        if ($this->getIndentation()) {
            $parts = array_map(function ($part) {
                return $this->getIndentation().$part;
            }, $parts);
        }

        return implode(\PHP_EOL, $parts);
    }

    public function setType(string $value)
    {
        $this->type_ = $value;

        return $this;
    }

    public function value($value = null)
    {
        // Act like a property getter when nothing is passed
        if (null === $value) {
            return $this->value_;
        }
        $this->value_ = $value;

        return $this;
    }

    public function asConstant()
    {
        $this->isConstant_ = true;

        return $this;
    }

    public function equals(ValueContainer $value)
    {
        return $this->name_ === $value->getName();
    }

    protected function setImports()
    {
        if ((null !== $this->type_) && drewlabs_core_strings_contains($this->type_, '\\')) {
            $this->type_ = $this->addClassPathToImportsPropertyAfter(function ($classPath) {
                return $this->getClassFromClassPath($classPath);
            })($this->type_);
        }

        return $this;
    }

    protected function setComments()
    {
        /**
         * @var string[]
         */
        $descriptors = array_filter((drewlabs_core_strings_is_str($this->descriptors_) ? [$this->descriptors_] : $this->descriptors_) ?? []);
        if (!empty($descriptors)) {
            // Add a line separator between the descriptors and other definitions
            $descriptors[] = '';
        }
        $this->comment_ = (new CommentModelFactory(true))->make(
            $this->descriptors_ ?
                ($this->type_ ? array_merge(
                    $descriptors ?? [],
                    [
                        "@var $this->type_",
                    ]
                ) : array_merge(
                    $descriptors ?? [],
                    [
                        '@var mixed',
                    ]
                )) : ($this->type_ ? [
                    "@var $this->type_",
                ] : [
                    '@var mixed',
                ])
        );

        return $this;
    }

    private function parsePropertyValue()
    {
        $value = $this->value_;
        // Return the object is an empry string or array is passed in
        if (drewlabs_core_strings_is_str($value) && empty($value)) {
            return '';
        }
        $isPHPClassDef = (drewlabs_core_strings_is_str($value) && (drewlabs_core_strings_contains($value, '\\') || drewlabs_core_strings_starts_with($value, 'new') || drewlabs_core_strings_ends_with($value, '::class')));
        if (\is_bool($value)) {
            $this->type_ = null === $this->type_ ? sprintf('%s', PHPTypes::BOOLEAN) : $this->type_;

            return "$value";
        } elseif (is_numeric($value) || $isPHPClassDef) {
            $this->type_ = null === $this->type_ ? (is_numeric($value) ? sprintf('%s|%s', PHPTypes::INT, PHPTypes::FLOAT) : sprintf('%s', PHPTypes::OBJECT)) : $this->type_;

            return "$value";
        } elseif (drewlabs_core_strings_is_str($value) && !$isPHPClassDef) {
            $this->type_ = null === $this->type_ ? sprintf('%s', PHPTypes::STRING) : $this->type_;

            return "\"$value\"";
        } elseif (drewlabs_core_array_is_arrayable($value)) {
            $this->type_ = null === $this->type_ ? sprintf('%s', PHPTypes::LIST) : $this->type_;
            $indentation = $this->getIndentation();
            if (empty($value)) {
                $start = '[]';
            } else {
                $start = '['.\PHP_EOL;
                foreach ($value as $key => $value) {
                    $def = (is_numeric($key) ? sprintf("\t\"%s\",", $value) : (is_numeric($value) ? sprintf("\t\"%s\" => %s,", $key, $value) : sprintf("\t\"%s\" => \"%s\",", $key, $value))).\PHP_EOL;
                    $start .= $indentation ? $indentation.$def : $def;
                }
                $start .= $indentation ? $indentation.']' : ']';
            }

            return $start;
        }

        return '';
    }
}
