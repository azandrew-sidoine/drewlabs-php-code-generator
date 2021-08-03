<?php

namespace Drewlabs\CodeGenerator\Models;

use Drewlabs\CodeGenerator\Contracts\PHPComponentModelInterface;
use Drewlabs\CodeGenerator\Exceptions\PHPClassException;
use Drewlabs\CodeGenerator\Models\PHPClassMethod;
use Drewlabs\CodeGenerator\Models\PHPClassProperty;
use Drewlabs\CodeGenerator\Models\Traits\HasTraitsDefintions;
use InvalidArgumentException;

/** @package Drewlabs\CodeGenerator\Models */
class PHPClass implements PHPComponentModelInterface
{
    use HasTraitsDefintions;


    /**
     *
     * @var string[]
     */
    private $interfaces_ = [];

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
        if (drewlabs_core_array_is_arrayable($implementations)) {
            foreach ($implementations as $value) {
                # code...
                if (!drewlabs_core_strings_is_str($value)) {
                    throw new InvalidArgumentException(sprintf("%s is not an istance of PHP string", get_class($value)));
                }
                $this->addImplementation($value);
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

    public function setBaseClass(string $baseClass)
    {
        if (null !== $baseClass) {
            $this->baseClass_ = $baseClass;
        }
        return $this;
    }

    public function addImplementation(string $value)
    {
        if (null !== $value) {
            $this->interfaces_[] = $value;
        }
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

    protected function setImports()
    {
        // Loop through interfaces
        $interfaces = [];
        foreach (($this->interfaces_ ?? []) as $value) {
            if (drewlabs_core_strings_contains($value, '\\')) {
                $interfaces[] = $this->addClassPathToImportsPropertyAfter(function($classPath) {
                    return $this->getClassFromClassPath($classPath);
                })($value);
            } else {
                $interfaces[] = $value;
            }
        }
        $this->interfaces_ = $interfaces;

        // Set base class imports
        if (drewlabs_core_strings_contains($this->baseClass_, '\\')) {
            $this->baseClass_ = $this->addClassPathToImportsPropertyAfter(function($classPath) {
                return $this->getClassFromClassPath($classPath);
            })($this->baseClass_);
        }
    }

    public function classToString(): string
    {
        $this->setImports();
        $parts = [];
        $modifier = $this->isFinal_ ? "final " : ($this->isAbstract_ ? "abstract " : "");
        $declaration = sprintf("%sclass %s", $modifier, $this->name_);
        if ((null !== $this->baseClass_)) {
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
        $imports = $this->getImports();

        if ((null !== $this->properties_) && is_array($this->properties_)  && !empty($this->properties_)) {
            foreach ($this->properties_ as $value) {
                $parts[] = "";
                $parts[] = $value->setIndentation("\t")->__toString();
                $imports = array_merge($imports, $value->getImports() ?? []);
            }
        }
        if ((null !== $this->methods_) && is_array($this->methods_)  && !empty($this->properties_)) {
            foreach ($this->methods_ as $value) {
                $parts[] = "";
                $parts[] = $value->setGlobalImports($imports)->setIndentation("\t")->__toString();
                $imports = array_merge($imports, $value->getImports() ?? []);
            }
        }
        $this->setGlobalImports($imports);
        $parts[] = "";
        $parts[] = "}";
        return implode(PHP_EOL, $parts);
    }

    protected function buildNamespaceClass()
    {
        $classString = $this->classToString();
        $parts[] = (new PHPNamespace($this->namespace_))
        ->addClass($this)
        ->addImports($this->getGlobalImports())->__toString();
        $parts[] = $classString;
        return implode(PHP_EOL, $parts);
    }

    public function __toString(): string
    {

        if (null !== $this->namespace_) {
            return $this->buildNamespaceClass();
        }
        return $this->classToString();
    }
}
