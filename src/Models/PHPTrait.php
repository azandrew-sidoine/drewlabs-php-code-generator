<?php

namespace Drewlabs\CodeGenerator\Models;

use Drewlabs\CodeGenerator\Contracts\OOPStructInterface;
use Drewlabs\CodeGenerator\Models\PHPClassMethod;
use Drewlabs\CodeGenerator\Models\PHPClassProperty;
use Drewlabs\CodeGenerator\Models\Traits\HasTraitsDefintions;
use InvalidArgumentException;

/** @package Drewlabs\CodeGenerator\Models */
class PHPTrait implements OOPStructInterface
{

    use HasTraitsDefintions;

    public function __construct(
        string $name,
        array $methods = [],
        array $properties = []
    ) {
        $this->name_ = $name;
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

    public function objectToString(): string
    {
        $parts = [];
        $parts[] =  sprintf("trait %s", $this->name_);
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

    protected function buildWithNamespaceDefinitions()
    {
        $traitString = $this->objectToString();
        $parts[] = (new PHPNamespace($this->namespace_))
        ->addTrait($this)
        ->addImports($this->getGlobalImports())->__toString();
        $parts[] = $traitString;
        return implode(PHP_EOL, $parts);
    }

    public function __toString(): string
    {

        if (null !== $this->namespace_) {
            return $this->buildWithNamespaceDefinitions();
        }
        return $this->objectToString();
    }
}
