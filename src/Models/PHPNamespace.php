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
     * Class definition model
     *
     * @var PHPClass
     */
    private $class_;

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
        $this->class_ = $class_;
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


    public function __toString(): string
    {
        $parts = [];
        // Add content here
        return implode(PHP_EOL, $parts);
    }
}