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

interface Blueprint extends OOPComposableStruct
{
    public function setBaseClass(string $baseClass);

    /**
     * Add an interface or an implementation to the class.
     *
     * @return self
     */
    public function addImplementation(string $value);

    /**
     * Creates a PHP static class definition.
     *
     * @return self
     */
    public function asFinal();

    /**
     * Creates a PHP abstract class definition.
     *
     * @return self
     */
    public function asAbstract();

    /**
     * Returns the list of interfaces that the blueprint implements.
     *
     * @return string[]
     */
    public function getImplementations(): ?array;

    /**
     * Returns the base class the blueprint definition.
     */
    public function getBaseClass(): ?string;

    /**
     * Checks if the blueprint definition is a final blueprint definition.
     */
    public function isFinal(): bool;

    /**
     * Checks if the blueprint definition is an abstract blueprint definition.
     */
    public function isAbstract(): bool;

    /**
     * Add a class path that will be added to the global import when generating class namespace.
     *
     * @return self
     */
    public function addClassPath(string $classPath);

    /**
     * Method that allow bluprint to import function using a use statement.
     *
     * @return self
     */
    public function addFunctionPath(string $functionPath);
}
