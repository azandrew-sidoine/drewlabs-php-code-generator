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

interface HasVisibility
{
    /**
     * Returns the visibility or access modifier of the component
     * 
     * @return null|string 
     */
    public function getVisibility(): ?string;

}