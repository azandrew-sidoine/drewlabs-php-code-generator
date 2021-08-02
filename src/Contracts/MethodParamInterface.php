<?php

namespace Drewlabs\CodeGenerator\Contracts;

interface MethodParamInterface
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
}
