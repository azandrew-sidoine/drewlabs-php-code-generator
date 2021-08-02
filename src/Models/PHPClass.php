<?php

namespace Drewlabs\CodeGenerator\Models;

use Drewlabs\CodeGenerator\CommentModelFactory;
use Drewlabs\CodeGenerator\Contracts\PHPComponentModelInterface;
use Drewlabs\CodeGenerator\Contracts\MethodParamInterface;
use Drewlabs\CodeGenerator\Exceptions\PHPClassException;
use Drewlabs\CodeGenerator\Models\Traits\HasImportDeclarations;
use Drewlabs\CodeGenerator\Models\PHPClassMethod;
use Drewlabs\CodeGenerator\Models\PHPClassProperty;
use InvalidArgumentException;

/** @package Drewlabs\CodeGenerator\Models */
class PHPClass implements PHPComponentModelInterface
{
    use HasImportDeclarations;

    /**
     * @var string
     */
    private $name_;
    /**
     * @var PHPClassMethod[]
     */
    private $methods_ = [];
    /**
     * @var PHPClassProperty[]
     */
    private $properties_ = [];

    /**
     * The namespace the class belongs to
     *
     * @var string
     */
    private $namespace_;

    /**
     *
     * @var string[]
     */
    private $interfaces_ = [];

    /**
     * List of packages and classes to import
     *
     * @var string[]
     */
    private $imports_;

    /**
     * List of traits
     *
     * @var string[]
     */
    private $traits_;

    /**
     * Class base class name
     *
     * @var string
     */
    private $baseClass_;

    /**
     * Create an abstract php class
     *
     * @var bool
     */
    private $isAbstract_;

    /**
     * Create a final php class
     *
     * @var bool
     */
    private $isFinal_;

    public function __construct(
        string $name,
        array $implementations = [],
        array $methods = [],
        array $properties = []
    ) {
        $this->name_ = $name;
        // Validate implementations
        foreach ($implementations as $value) {
            # code...
            if (!drewlabs_core_strings_is_str($value)) {
                throw new InvalidArgumentException(sprintf("%s is not an istance of PHP string", get_class($value)));
            }
        }
        // Validate methods
        if (null !== $methods && is_array($methods)) {
            foreach ($methods as $value) {
                # code...
                if (!($value instanceof PHPClassMethod)) {
                    throw new InvalidArgumentException(sprintf("%s is not an istance of %s", get_class($value), PHPClassMethod::class));
                }
                $this->addMethod($value);
            }
        }

        // Validate and add properties properties
        if (null !== $properties && is_array($methods)) {
            foreach ($properties as $value) {
                # code...
                if (!($value instanceof PHPClassProperty)) {
                    throw new InvalidArgumentException(sprintf("%s is not an istance of %s", get_class($value), PHPClassProperty::class));
                }
                $this->addProperty($value);
            }
        }
    }

    public function getName()
    {
        return $this->name_;
    }

    public function addProperty(PHPClassProperty $property)
    {
        $this->properties_[] = $property;
        return $this;
    }

    public function addMethod(PHPClassMethod $property)
    {
        $this->methods_[] = $property;
        return $this;
    }

    public function setBaseClass(string $baseClass)
    {
        if (null !== $baseClass) {
            if (drewlabs_core_strings_contains($baseClass, '\\')) {
                if (!in_array($baseClass, $this->imports_)) {
                    $this->imports_[] = $baseClass;
                }
                $this->baseClass_[] = drewlabs_core_strings_after_last('\\', $baseClass);
            } else {
                $this->baseClass_[] = $baseClass;
            }
        }
        return $this;
    }

    public function addImplementation(string $value)
    {
        if (null !== $value) {
            if (drewlabs_core_strings_contains($value, '\\')) {
                if (!in_array($value, $this->imports_)) {
                    $this->imports_[] = $value;
                }
                $this->interfaces_[] = drewlabs_core_strings_after_last('\\', $value);
            } else {
                $this->interfaces_[] = $value;
            }
        }
        return $this;
    }

    public function addToNamespace(string $namespace)
    {
        $this->namespace_ = $namespace;
        return $this;
    }

    public function addTrait(string $trait)
    {
        $this->traits_[] = $trait;
        return $this;
    }

    public function isFinal(bool $value)
    {
        if ($this->isAbstract_) {
            throw new PHPClassException("Class cannot be final and abstract at the same time");
        }
        $this->isFinal_ = $value;
        return $this;
    }

    public function isAbstract(bool $value)
    {
        if ($this->isFinal_) {
            throw new PHPClassException("Class cannot be final and abstract at the same time");
        }
        $this->isAbstract_ = $value;
        return $this;
    }

    public function classToString(): string
    {
        $parts = [];
        $modifier = $this->isFinal_ ? "final " : ($this->isAbstract_ ? "abstract " : "");
        $declaration = sprintf("%sclass %s", $modifier, $this->name_);
        if ((null !== $this->baseClass_) && is_array($this->baseClass_) && !empty($this->baseClass_)) {
            $declaration .= sprintf(" extends %s", $this->baseClass_);
        }
        if ((null !== $this->interfaces_)  && is_array($this->interfaces_) && !empty($this->interfaces_)) {
            $declaration .= sprintf(" implements %s", implode(", ", $this->interfaces_));
        }
        $parts[] =  $declaration;
        $parts[] = "{";
        // Add Traits
        if (null !== $this->traits_ && is_array($this->traits_) && !empty($this->traits_)) {
            $parts[] = "";
            foreach ($this->traits_ as $value) {
                # code...
                $parts[] = "\tuse $value;";
            }
        }
        // Add properties
        if ((null !== $this->properties_) && is_array($this->properties_)  && !empty($this->properties_)) {
            foreach ($this->properties_ as $value) {
                $parts[] = "";
                $parts[] = $value->setIndentation("\t")->__toString();
            }
        }
        if ((null !== $this->methods_) && is_array($this->methods_)  && !empty($this->properties_)) {
            foreach ($this->methods_ as $value) {
                $parts[] = "";
                $parts[] = $value->setIndentation("\t")->__toString();
            }
        }
        $parts[] = "";
        $parts[] = "}";
        return implode(PHP_EOL, $parts);
    }

    public function __toString(): string
    {
        $imports = array_merge(
            $this->imports_ ?? [],
            array_reduce(
                $this->properties_ ?? [],
                function ($carry, $prop) {
                    return array_merge($carry, $prop->getImports());
                },
                []
            ),
            array_reduce(
                $this->methods_ ?? [],
                function ($carry, $prop) {
                    return array_merge($carry, $prop->getImports());
                },
                []
            ),
        );

        if (null !== $this->namespace_) {
            return (new PHPNamespace($this->namespace_))
                ->addClass($this)
                ->addImports($imports);
        }
        return $this->classToString();
    }
}
