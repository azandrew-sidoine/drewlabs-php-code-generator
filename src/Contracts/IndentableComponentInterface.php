<?php

namespace Drewlabs\CodeGenerator\Contracts;

interface IndentableComponentInterface extends OOPComponentInterface
{
    /**
     * Set the indentation to apply to the component
     * 
     * @param string $indentation
     * @return self
     */
    public function setIndentation(string $indentation);

    /**
     * Return the indentation to add to the component definition
     *
     * @return string
     */
    public function getIndentation();
}