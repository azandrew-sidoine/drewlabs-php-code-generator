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

interface ClassPathImportContainer
{
    /**
     * List of component imports.
     */
    public function getImports(): array;

    /**
     * Set the list of global imports definitions on the component.
     *
     * @return self|mixed
     */
    public function setGlobalImports(array $values);

    /**
     * Returns the list of imports of the container.
     */
    public function getGlobalImports(): array;
}
