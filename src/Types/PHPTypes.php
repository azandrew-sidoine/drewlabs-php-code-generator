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

class PHPTypes
{
    /** PHP `integer` type enum value  */
    public const INT = 'integer';

    /** PHP `float` type enum value  */
    public const FLOAT = 'float';

    /** PHP `float` type enum value  */
    public const DECIMAL = 'float';

    /** PHP `string` type enum value  */
    public const STRING = 'string';

    /** PHP `object` type enum value  */
    public const OBJECT = 'object';

    /** PHP `stdClass` type enum value  */
    public const STANDARD_CLASS = '\\stdClass';

    /** PHP `list` or `array` type enum value  */
    public const LIST = 'array';

    /** PHP `bool` type enum value  */
    public const BOOLEAN = 'bool';

    /** PHP `mixed` type enum value  */
    public const ANY = 'mixed';

    /** @var string[] */
    public const VALUES = [
        self::INT,
        self::FLOAT,
        self::DECIMAL,
        self::STRING,
        self::OBJECT,
        self::STANDARD_CLASS,
        self::LIST,
        self::BOOLEAN,
    ];
}
