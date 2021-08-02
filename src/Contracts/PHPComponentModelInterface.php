<?php

namespace Drewlabs\CodeGenerator\Contracts;

interface PHPComponentModelInterface extends Stringable
{
    /**
     * Return component name
     *
     * @return string
     */
    public function getName();
}