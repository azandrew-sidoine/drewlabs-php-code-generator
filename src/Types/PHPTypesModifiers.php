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
    /**
     * PHP class component private modifier.
     *
     * @var string
     */
    public const PRIVATE = 'private';
    /**
     * PHP class component public modifier.
     *
     * @var string
     */
    public const PUBLIC = 'public';

    /**
     * PHP class component public modifier.
     *
     * @var string
     */
    public const PROTECTED = 'protected';

    /**
     * String representation of php constant type.
     */
    public const CONSTANT = 'const';

    /** @var string[] */
    public const VALUES = [
        self::PRIVATE,
        self::PUBLIC,
        self::PROTECTED,
        self::CONSTANT,
    ];
}
