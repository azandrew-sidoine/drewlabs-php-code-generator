<?php

namespace Drewlabs\CodeGenerator\Models\Traits;

trait HasImportDeclarations
{

    /**
     * List of imports to append to the file/class imports
     *
     * @var string[]
     */
    private $imports_;

    public function getImports(): array
    {
        return $this->imports_ ?? [];
    }
}