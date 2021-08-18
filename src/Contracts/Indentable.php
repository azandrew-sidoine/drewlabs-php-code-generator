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

interface Indentable extends OOPComponentInterface
{
    /**
     * Set the indentation to apply to the component.
     *
     * @return self
     */
    public function setIndentation(string $indentation);

    /**
     * Return the indentation to add to the component definition.
     *
     * @return string
     */
    public function getIndentation();
}
