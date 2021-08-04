<?php

namespace Drewlabs\CodeGenerator\DocComments;

class Keywords
{
    public const PARAMS = '@param';
    public const VARS = '@var';
    public const THROWS = '@throws';
    public const RETURNS = '@return';
    public const AUTHOR = '@author';

    public const VALUES = [
        self::PARAMS,
        self::VARS,
        self::THROWS,
        self::RETURNS,
        self::AUTHOR,
    ];
}