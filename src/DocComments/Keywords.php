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
