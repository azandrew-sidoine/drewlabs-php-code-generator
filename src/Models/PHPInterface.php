<?php

namespace Drewlabs\CodeGenerator\Models;

use Drewlabs\CodeGenerator\Contracts\OOPComponentInterface;
use Drewlabs\CodeGenerator\Models\PHPClassMethod;
use Drewlabs\CodeGenerator\Models\PHPClassProperty;
use Drewlabs\CodeGenerator\Models\Traits\OOPStructComponent;
use InvalidArgumentException;

/** @package Drewlabs\CodeGenerator\Models */
class PHPInterface implements OOPComponentInterface
{
    use OOPStructComponent;

    /**
     * Class base class name
     *
     * @var string
     */
    private $baseInterface_;


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

    public function setBaseInterface(string $value)
    {
        if (null !== $value) {
            $this->baseInterface_ = $value;
        }
        return $this;
    }

    protected function setImports()
    {
        // Set base class imports
        if (drewlabs_core_strings_contains($this->baseInterface_, '\\')) {
            $this->baseInterface_ = $this->addClassPathToImportsPropertyAfter(function($classPath) {
                return $this->getClassFromClassPath($classPath);
            })($this->baseInterface_);
        }
    }

    public function objectToString(): string
    {
        $this->setImports();
        $parts = [];
        $declaration = sprintf("interface %s", $this->name_);
        if ((null !== $this->baseInterface_)) {
            $declaration .= sprintf(" extends %s", $this->baseInterface_);
        }
        $parts[] =  $declaration;
        $parts[] = "{";
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
                $parts[] = $value->asInterfaceMethod()->setGlobalImports($imports)->setIndentation("\t")->__toString();
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
        $result = $this->objectToString();
        $parts[] = (new PHPNamespace($this->namespace_))
        ->addInterface($this)
        ->addImports($this->getGlobalImports())->__toString();
        $parts[] = $result;
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
