<?php

namespace Drewlabs\CodeGenerator\Contracts;

interface ComponentFactoryInterface
{
    /**
     * Create a PHP component model definition
     *
     * @param array ...$args
     * @return Stringable
     */
    public function make(...$args);
}