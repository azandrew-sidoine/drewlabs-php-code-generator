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

interface FunctionParameterInterface
{
    /**
     * Returns the parameter name.
     *
     * @return string
     */
    public function name();

    /**
     * Returns the parameter type name.
     *
     * @return string
     */
    public function type();

    /**
     * Returns the parameter default value.
     *
     * @return string
     */
    public function defaultValue();

    /**
     * Indicates that the parameter is optional.
     *
     * @return bool
     */
    public function isOptional();

    /**
     * Creates an optional method / function parameter.
     *
     * @return static
     */
    public function asOptional();

    /**
     * Checks if the current parameter definition equals the value passed as parameter.
     *
     * @return bool
     */
    public function equals(self $value);

    /**
     * Makes the parameter an ellipsis by adding (...) in front of the parameter.
     *
     * @return static
     */
    public function asVariadic();

    /**
     * Boolean value returns indicating the type of parameter.
     *
     * @return static
     */
    public function isVariadic();

    /**
     * Creates a reference paramater to a callable.
     *
     * @return static
     */
    public function asReference();

    /**
     * Boolean value returns show if the parameter is a reference parameter or not.
     *
     * @return static
     */
    public function isReference();
}
