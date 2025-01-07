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

namespace Drewlabs\CodeGenerator\Helpers;

class Types
{

    /**
     * Checks if value provided for a variable is a class declaration 
     * @param mixed $value 
     * @return bool 
     */
    public static function isClassDeclaration($value)
    {
        return (is_string($value) && (Str::contains($value, '\\') || Str::startsWith($value, 'new') || Str::endsWith($value, '::class')));
    }
}
