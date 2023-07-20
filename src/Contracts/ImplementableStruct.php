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

interface ImplementableStruct extends PathImportContainer, NamespaceComponent
{

    /**
     * Return component name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the base interface that the component inherit from.
     */
    public function getBaseInterface(): ?string;

    /**
     * @deprecated v0.2.x
     * The interface does not express the multiple inheritance ability of PHP interfaces. Use `addBaseInterface()` instead
     * 
     * Set the base interface that the component inherit from.
     * 
     */
    public function setBaseInterface(string $value): self;

    /**
     * Add a base contract/interface to the current interface
     * 
     * @param string $value
     * 
     * @return static 
     */
    public function addBaseInterface(string $value);

    /**
     * Returns the list of methods of the current component.
     *
     * @return CallableInterface[]
     */
    public function getMethods(): array;

    /**
     * Returns the namespace that the current class belongs to.
     */
    public function getNamespace(): ?string;
}
