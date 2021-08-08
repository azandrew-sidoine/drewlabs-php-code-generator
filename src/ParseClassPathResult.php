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
    private $name_;

    /**
     * Class path.
     *
     * @var string
     */
    private $classPath_;

    public function __construct(string $name, string $classPath)
    {
        $this->name_ = $name;
        $this->classPath_ = $classPath;
    }

    public function getClassPath()
    {
        return $this->classPath_;
    }

    public function getComponentName()
    {
        return $this->name_;
    }
}
