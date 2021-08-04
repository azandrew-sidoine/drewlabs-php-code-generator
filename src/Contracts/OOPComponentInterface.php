<?php

namespace Drewlabs\CodeGenerator\Contracts;

interface OOPComponentInterface extends Stringable
{
    /**
     * Return component name
     *
     * @return string
     */
    public function getName();
}