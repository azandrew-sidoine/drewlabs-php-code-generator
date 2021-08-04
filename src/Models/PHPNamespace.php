<?php

namespace Drewlabs\CodeGenerator\Models;

use Drewlabs\CodeGenerator\Contracts\Stringable;

/** @package Drewlabs\CodeGenerator\Models */
class PHPNamespace implements Stringable
{

    /**
     *
     * @var string
     */
    private $ns_;

    /**
     * Class definition models
     *
     * @var PHPClass[]
     */
    private $class_ = [];

    /**
     * Trait definition models
     *
     * @var PHPTrait[]
     */
    private $traits_ = [];

    /**
     * Interfaces definition models
     *
     * @var PHPInterface[]
     */
    private $interfaces_ = [];

    /**
     * Undocumented function
     *
     * @param string $ns
     */
    public function __construct(string $ns)
    {
        $this->ns_ = $ns;
    }

    /**
     * Add a class to the namespace
     *
     * @param PHPClass $class_
     * @return self
     */
    public function addClass(PHPClass $class_)
    {
        $this->class_[] = $class_;
        return $this;
    }

    /**
     * Add a trait to the namespace
     *
     * @param PHPTrait $value
     * @return self
     */
    public function addTrait(PHPTrait $value)
    {
        $this->traits_[] = $value;
        return $this;
    }

    /**
     * Add an interface to the namespace
     *
     * @param PHPInterface $value
     * @return self
     */
    public function addInterface(PHPInterface $value)
    {
        $this->interfaces_[] = $value;
        return $this;
    }

    /**
     * Add list of imports to the namespace definitions
     *
     * @param string[] $imports
     * @return self
     */
    public function addImports(array $imports)
    {
        $this->imports_ = $imports;
        return $this;
    }

    public function buildClasses()
    {
        $classes = [];
        foreach (($this->class_ ?? []) as $value) {
            $classes[$value->getName()] = $value->addToNamespace($this->ns_)->__toString();
        }
        return $classes;
    }

    public function buildTraits()
    {
        $traits = [];
        foreach (($this->traits_ ?? []) as $value) {
            $traits[$value->getName()] = $value->addToNamespace($this->ns_)->__toString();
        }
        return $traits;
    }


    public function __toString(): string
    {
        $parts = $this->ns_ ? ["namespace $this->ns_;"] : [];
        $imports = array_map(function($import) {
            return is_string($import) ? drewlabs_core_strings_ltrim($import, "\\") : $import;
        }, $this->imports_);
        $parts[] = "";
        foreach ($imports as $value) {
            $parts[] = "use $value;";
        }
        $parts[] = "";
        // Add content here
        return implode(PHP_EOL, $parts);
    }
}