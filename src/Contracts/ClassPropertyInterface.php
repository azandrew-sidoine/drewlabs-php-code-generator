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

namespace Drewlabs\CodeGenerator\Contracts;

interface ClassPropertyInterface extends ClassMemberInterface
{
    /**
     * Type property setter.
     *
     * @return self
     */
    public function setType(string $type);

    /**
     * Set the property default value definition or returns it it null is passed.
     *
     * @param array|string $value
     *
     * @return mixed|self
     */
    public function value($value = null);

    /**
     * Create the property as PHP Constant.
     *
     * @return self|ClassPropertyInterface
     */
    public function asConstant();

    /**
     * Checks if two properties are same.
     *
     * @return bool
     */
    public function equals(self $value);
}
