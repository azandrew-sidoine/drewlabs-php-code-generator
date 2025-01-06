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

namespace Drewlabs\CodeGenerator;

class ParseClassPathResult
{
    /**
     * Component name.
     *
     * @var string
     */
    private $name;

    /**
     * Class path.
     *
     * @var string
     */
    private $classPath;

    /**
     * Class instance initializer
     * 
     * @param string $name 
     * @param null|string $classPath 
     */
    public function __construct(string $name, ?string $classPath = null)
    {
        $this->name = $name;
        $this->classPath = $classPath;
    }

    public function getClassPath()
    {
        return $this->classPath;
    }

    public function getComponentName()
    {
        return $this->name;
    }
}
