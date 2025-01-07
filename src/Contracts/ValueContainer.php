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

interface ValueContainer extends Indentable, PathImportContainer, NamespaceComponent
{
    /**
     * Return component name.
     *
     * @return string
     */
    public function getName();
    
    /**
     * Type property setter.
     *
     * @return static
     */
    public function setType(string $type);


    /**
     * Returns type definition of the instance
     * 
     * @return null|string 
     */
    public function getType(): ?string;

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
     * @return static
     */
    public function asConstant();

    /**
     * Checks if two properties are same.
     *
     * @return bool
     */
    public function equals(self $value);
}
