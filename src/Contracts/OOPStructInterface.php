<?php

declare(strict_types=1);

/*
 * This file is part of the Drewlabs package.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\CodeGenerator\Contracts;

interface OOPStructInterface extends OOPComponentInterface, ClassPathImportContainer
{
    /**
     * Adds a constant property to the oop component definition.
     *
     * @param PHPClassProperty $property
     *
     * @return self
     */
    public function addConstant(ClassPropertyInterface $property);

    /**
     * Add a property to the oop component definition.
     *
     * @return self
     */
    public function addProperty(ClassPropertyInterface $property);

    /**
     * Add a method definition to the oop component definition.
     *
     * @return self
     */
    public function addMethod(CallableInterface $property);

    /**
     * Add the oop component to a namespace.
     *
     * @return self
     */
    public function addToNamespace(string $namespace);

    // Add members getters

    /**
     * Returns the list of methods of the current component.
     *
     * @return CallableInterface[]
     */
    public function getMethods(): array;

    /**
     * Returns the list of properties of the current component.
     *
     * @return ClassPropertyInterface[]
     */
    public function getProperties(): array;

    /**
     * Returns the namespace that the current class belongs to.
     */
    public function getNamespace(): ?string;
}
