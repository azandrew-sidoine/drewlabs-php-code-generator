<?php

namespace Drewlabs\CodeGenerator\Contracts;

interface OOPStructInterface extends OOPComponentInterface
{
    /**
     * Adds a constant property to the oop component definition
     *
     * @param PHPClassProperty $property
     * @return self
     */
    public function addConstant(ClassPropertyInterface $property);

    /**
     * Add a property to the oop component definition
     *
     * @param ClassPropertyInterface $property
     * @return self
     */
    public function addProperty(ClassPropertyInterface $property);

    /**
     * Add a method definition to the oop component definition
     *
     * @param ClassMethodInterface $property
     * @return self
     */
    public function addMethod(ClassMethodInterface $property);

    /**
     * Add the oop component to a namespace
     *
     * @param string $namespace
     * @return self
     */
    public function addToNamespace(string $namespace);
}