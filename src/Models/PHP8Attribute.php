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

namespace Drewlabs\CodeGenerator\Models;

class PHP8Attribute
{
    /** @var string */
    private $def;

    /**
     * Class instance instance initializer
     * 
     * @param string $def
     * 
     */
    public function __construct(string $def)
    {
        $this->def = $def;
    }

    /**
     * Class factory constructor
     * 
     * @param string $def 
     * @return static 
     */
    public static function new(string $def)
    {
        return new static($def);
    }


    public function __toString()
    {
        return sprintf("#[%s]", $this->def);
    }
}
