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

interface PropertyInterface
{

    /**
     * Checks if property must have a mutator definition
     * 
     * @return bool 
     */
    public function hasMutator(): bool;

    /**
     * Checks if property must have a accessor definition
     * 
     * @return bool 
     */
    public function hasAccessor(): bool;

    
    /**
     * In case property provides a mutator, check if mutator must be
     * generated with PHP `clone` statement to create a copy of the object.
     * 
     * @return bool 
     */
    public function isImmutable(): bool;
}
