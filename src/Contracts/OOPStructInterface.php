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

interface OOPStructInterface extends PathImportContainer, NamespaceComponent
{
    /**
     * Return component name.
     *
     * @return string
     */
    public function getName();

    /**
     * Adds a constant property to the oop component definition.
     *
     * @param PHPClassProperty $property
     *
     * @return static
     */
    public function addConstant(ValueContainer $property);

    /**
     * Add a property to the oop component definition.
     *
     * @return static
     */
    public function addProperty(ValueContainer $property);

    /**
     * Add a method definition to the oop component definition.
     *
     * @return static
     */
    public function addMethod(CallableInterface $property);

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
     * @return ValueContainer[]
     */
    public function getProperties(): array;
}
