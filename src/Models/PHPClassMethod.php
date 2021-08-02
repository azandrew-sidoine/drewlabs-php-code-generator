<?php

namespace Drewlabs\CodeGenerator\Models;

use Drewlabs\CodeGenerator\CommentModelFactory;
use Drewlabs\CodeGenerator\Contracts\PHPComponentModelInterface;
use Drewlabs\CodeGenerator\Contracts\MethodParamInterface;
use Drewlabs\CodeGenerator\Models\Traits\HasImportDeclarations;
use Drewlabs\CodeGenerator\Models\Traits\HasIndentation;

/** @package Drewlabs\CodeGenerator\Models */
class PHPClassMethod implements PHPComponentModelInterface
{

    use HasImportDeclarations;
    use HasIndentation;

    /**
     * @var string
     */
    private $name_;
    /**
     * @var MethodParamInterface[]
     */
    private $params_;
    /**
     * @var string
     */
    private $description_;

    /**
     * PHP Stringeable component
     * 
     * @var mixed
     */
    private $comment_;

    /**
     *
     * @var string
     */
    private $accessModifier_;

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

    public function __construct(
        string $name,
        array $params = [],
        string $returns = null,
        $modifier = 'public',
        $description = ''
    ) {
        $this->name_ = $name;
        $this->params_ = $params;
        $this->description_ = $description;
        $this->accessModifier_ = $modifier;
        $this->returns_ = $returns;
    }

    public function getName()
    {
        return $this->name_;
    }

    /**
     * Specify the exceptions that the current method throws
     *
     * @param array $exceptions
     * @return self
     */
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

    public function isStatic(bool $value)
    {
        $this->isStatic_ = $this->name_ === '__construct' ? false : ($value || false);
        return $this;
    }

    /**
     * Add contents to the generated method
     *
     * @param string $content
     * @return self
     */
    public function withConents(string $content)
    {
        $this->content_ = $content;
        return $this;

    }

    /**
     * Add a description to the class
     *
     * @param string $value
     * @return self
     */
    public function withDescriptions(string $value)
    {
        $this->description_ = $value;
        return $this;

    }

    protected function setFileImports()
    {
        if (null !== $this->returns_) {
            if (drewlabs_core_strings_contains($this->returns_, '\\')) {
                $this->imports_[] = $this->returns_;
                $this->returns_ = drewlabs_core_strings_after_last('\\', $this->returns_);
            }
        }
        $values = [];
        if ((null !== $this->params_)) {
            $values = is_array($this->params_) ? $this->params_ : (is_string($this->params_) ? [$this->params_] : []);
            $params = [];
            foreach ($values as $value) {
                if (drewlabs_core_strings_contains($value->type(), '\\')) {
                    $this->imports_[] = $value;
                    $params[] = new MethodParam($value->name(), drewlabs_core_strings_after_last('\\', $value->type()), $value->defaultValue());
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
        $descriptors = $this->description_ ? [$this->description_] : [];
        // Generates method params comments
        if (null !== $this->params_) {
            foreach ($this->params_ as $value) {
                $type = null === $value->type() ? "mixed" : $value->type();
                $descriptors[] = "@param " . $type . " " . $value->name();
            }
        }
        $descriptors[] = "";
        // Generate exception comment
        if ((null !== $this->exceptions_) && is_array($this->exceptions_)) {
            foreach ($this->exceptions_ as $value) {
                $descriptors[] = "@throws $value";
            }
            $descriptors[] = "";
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
        ) ? $this->accessModifier_ : "public";
        // Start the declaration
        $declaration = $this->isStatic_ ? "$accessModifier static function $this->name_(" : "$accessModifier function $this->name_(";
        // Add method params
        if (null !== $this->params_) {
            $params = array_map(function ($param) {
                $type = null === $param->type() ? "" : $param->type();
                $result = "$type \$" . $param->name();
                return null !== $param->defaultValue() ? $result : "$result = " . $param->defaultValue();
            }, $this->params_);
            $declaration .= implode(", ", $params) . ")";
        }
        $parts[] = $declaration;
        $parts[] = "{";
        $parts[] = "\t# code...";
        if (null !== $this->content_) {
            $splitted_contents = explode(PHP_EOL, $this->content_);
            $splitted_contents = array_map(function($content) {
                return "\t$content";
            }, $splitted_contents);
            $parts[] = implode(PHP_EOL, $splitted_contents);
        }
        $parts[] = "}";
        if ($this->getIndentation()) {
            $parts = array_map(function($part) {
                return $this->getIndentation() . "$part";
            }, $parts);
        }
        return implode(PHP_EOL, $parts);
    }
}
