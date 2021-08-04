<?php

namespace Drewlabs\CodeGenerator\Contracts;

/** @package Drewlabs\CodeGenerator\Contracts */
interface Blueprint extends OOPStructInterface
{
    public function setBaseClass(string $baseClass);

    /**
     * Add an interface or an implementation to the class
     *
     * @param string $value
     * @return self
     */
    public function addImplementation(string $value);

    /**
     * Creates a PHP static class definition
     *
     * @return self
     */
    public function asFinal();
    /**
     * Creates a PHP abstract class definition
     *
     * @return self
     */
    public function asAbstract();
}