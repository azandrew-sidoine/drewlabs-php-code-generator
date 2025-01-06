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


interface HasPHP8Attributes
{
    /**
     * Add a PHP8 attribute to the component
     * 
     * @return static 
     */
    public function addAttribute(string $value);

    /**
     * Get the list of PHP8 attributes
     * 
     * @return static 
     */
    public function getAttributes(): array;
}
