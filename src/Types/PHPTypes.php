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
    /**
     * String representation of php integer type.
     */
    public const INT = 'integer';

    /**
     * String representation of php floating point type.
     */
    public const FLOAT = 'float';

    /**
     * String representation of php decimal type.
     */
    public const DECIMAL = 'float';

    /**
     * String representation of php string type.
     */
    public const STRING = 'string';

    /**
     * String representation of php objects.
     */
    public const OBJECT = 'object';

    /**
     * String representation of php standard class.
     */
    public const STANDARD_CLASS = '\\stdClass';

    /**
     * String representation of php list type.
     */
    public const LIST = 'array';

    /**
     * String representation of a boolean value.
     */
    public const BOOLEAN = 'bool';

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
