<?php

namespace Drewlabs\CodeGenerator\Contracts;

interface FunctionParameterInterface
{

    /**
     * Returns the parameter name
     *
     * @return string
     */
    public function name();

    /**
     * Returns the parameter type name
     *
     * @return string
     */
    public function type();

    /**
     * Returns the parameter default value
     *
     * @return string
     */
    public function defaultValue();

        /**
     * Indicates that the parameter is optional
     *
     * @return boolean
     */
    public function isOptional();

    /**
     * Creates an optional method / function parameter
     *
     * @return self
     */
    public function asOptional();

    /**
     * Checks if the current parameter definition equals the value passed as parameter
     *
     * @param self $value
     * @return bool
     */
    public function equals(self $value);
}
