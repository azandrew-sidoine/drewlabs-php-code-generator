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

namespace Drewlabs\CodeGenerator\Types;

class PHPTypesModifiers
{
    /** PHP `private` access modifier enum value */
    public const PRIVATE = 'private';

    /** PHP `public` access modifier enum value */
    public const PUBLIC = 'public';

    /** PHP `protected` access modifier enum value */
    public const PROTECTED = 'protected';

    /** PHP `const` access modifier enum value */
    public const CONSTANT = 'const';

    /** @var string[] */
    public const VALUES = [
        self::PRIVATE,
        self::PUBLIC,
        self::PROTECTED,
        self::CONSTANT,
    ];
}
