<?php

namespace Drewlabs\CodeGenerator\Types;

class PHPTypesModifiers
{
    /**
     * PHP class component private modifier
     * 
     * @var string
     */
    public const PRIVATE = 'private';
    /**
     * PHP class component public modifier
     * 
     * @var string
     */
    public const PUBLIC = 'public';

    /**
     * PHP class component public modifier
     * 
     * @var string
     */
    public const PROTECTED = 'protected';

    /**
     * String representation of php constant type
     */
    const CONSTANT = 'const';


    public const VALUES = [
        self::PRIVATE,
        self::PUBLIC,
        self::PROTECTED,
        self::CONSTANT
    ];
}