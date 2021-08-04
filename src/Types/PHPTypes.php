<?php

namespace Drewlabs\CodeGenerator\Types;

/** @package Drewlabs\CodeGenerator\Types */
class PHPTypes
{
    /**
     * String representation of php integer type
     */
    const INT = 'integer';
    /**
     * String representation of php floating point type
     */
    const FLOAT = 'float';
    /**
     * String representation of php decimal type
     */
    const DECIMAL = 'float';
    /**
     * String representation of php string type
     */
    const STRING = 'string';
    /**
     * String representation of php objects
     */
    const OBJECT = 'object';
    /**
     * String representation of php standard class
     */
    const STANDARD_CLASS = '\\stdClass';

    /**
     * String representation of php list type
     */
    const LIST = 'array';

    public const VALUES = [
        self::INT,
        self::FLOAT,
        self::DECIMAL,
        self::STRING,
        self::OBJECT,
        self::STANDARD_CLASS,
        self::LIST,
    ];
}
