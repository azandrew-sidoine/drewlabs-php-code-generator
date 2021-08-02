<?php

namespace Drewlabs\CodeGenerator\Models;

use Drewlabs\CodeGenerator\CommentModelFactory;
use Drewlabs\CodeGenerator\Contracts\PHPComponentModelInterface;
use Drewlabs\CodeGenerator\Models\Traits\HasImportDeclarations;
use Drewlabs\CodeGenerator\Models\Traits\HasIndentation;

class PHPClassProperty implements PHPComponentModelInterface
{

    use HasImportDeclarations;
    use HasIndentation;

    /**
     * @var string
     */
    private $name_;
    /**
     * @var string
     */
    private $type_;
    /**
     * @var string
     */
    private $description_;

    /**
     * List of imports to append to the file/class imports
     *
     * @var string[]
     */
    private $imports_;

    /**
     * PHP Stringeable component
     * @var mixed
     */
    private $comment_;

    /**
     *
     * @var string
     */
    private $accessModifier_;

    /**
     * The default value to set the property to
     *
     * @var string|array
     */
    private $value_;

    /**
     * Class instances initializer
     *
     * @param string $name
     * @param string|null $type
     * @param string $modifier
     * @param string|array $default
     * @param string $description
     */
    public function __construct(
        string $name,
        string $type = null,
        $modifier = 'public',
        $default = null,
        $description = ''
    ) {
        $this->name_ = $name;
        $this->type_ = $type;
        $this->description_ = $description;
        $this->accessModifier_ = $modifier;
        $this->value_ = $default;
    }

    public function getName()
    {
        return $this->name_;
    }

    protected function setFileImports()
    {
        if ((null !== $this->type_) && drewlabs_core_strings_contains($this->type_, '\\')) {
            $this->imports_[] = $this->type_;
            $this->type_ = drewlabs_core_strings_after_last('\\', $this->type_);
        }
        return $this;
    }

    protected function setComments()
    {
        $this->comment_ = (new CommentModelFactory(true))->make(
            $this->description_ ?
                ($this->type_ ? [
                    "$this->description_",
                    "@var $this->type_"
                ] : [
                    "$this->description_",
                    "@var mixed"
                ]) : ($this->type_ ? [
                    "@var $this->type_"
                ] : [
                    "@var mixed"
                ])
        );
        return $this;
    }

    public function setDefaultValue()
    {
        if ((null === $this->value_) || empty($this->value_)) {
            return $this;
        }
        if (drewlabs_core_strings_is_str($this->value_)) {
            $this->value_ = "$this->value_;";
        }

        if (drewlabs_core_array_is_arrayable($this->value_)) {
            $parts[] = "[";
            foreach ($this->value_ as $key => $value) {
                $parts[] = is_numeric($key) ? sprintf("\t\"%s\",", $value) : (is_numeric($value) ? sprintf("\t\"%s\" => %s,", $key, $value) : sprintf("\t\"%s\" => \"%s\",", $key, $value));
            }
            $parts[] = " ];" . PHP_EOL;
            $this->value_ = implode(PHP_EOL, $parts);
        }
        return $this;
    }

    public function __toString(): string
    {
        $this->setFileImports()
            ->setComments()
            ->setDefaultValue();
        // Generate comments
        if ($this->getIndentation()) {
            $parts[] = $this->comment_->setIndentation($this->getIndentation())->__toString();
        } else {
            $parts[] = $this->comment_->__toString();
        }
        // Generate defintion / declarations
        $definition = (null !== $this->accessModifier_) && in_array(
            $this->accessModifier_,
            [
                'private', 'protected', 'public'
            ]
        ) ? "$this->accessModifier_ \$$this->name_" : "public \$$this->name";

        if ($this->value_ && is_string($this->value_) && !empty($this->value_)) {
            $definition .= sprintf(" = %s", $this->value_);
        } else {
            $definition .= ";";
        }
        $parts[] = $definition;
        if ($this->getIndentation()) {
            $parts = array_map(function ($part) {
                return $this->getIndentation() . "$part";
            }, $parts);
        }
        return implode(PHP_EOL, $parts);
    }
}
