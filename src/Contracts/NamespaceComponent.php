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

interface NamespaceComponent extends Stringable
{
    /**
     * Add the oop component to a namespace.
     *
     * @return static
     */
    public function addToNamespace(?string $namespace = null);

    /**
     * Returns the namespace that the current component belongs to.
     */
    public function getNamespace(): ?string;
}
