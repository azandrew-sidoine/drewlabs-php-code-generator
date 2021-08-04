<?php

namespace Drewlabs\CodeGenerator\Contracts;

interface ClassPathImportContainer
{
    /**
     * List of component imports
     *
     * @return array
     */
    public function getImports(): array;

    /**
     * Set the list of global imports definitions on the component
     *
     * @param array $values
     * @return self|mixed
     */
    public function setGlobalImports(array $values);
}