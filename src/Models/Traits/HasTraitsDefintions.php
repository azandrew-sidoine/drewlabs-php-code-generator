<?php

namespace Drewlabs\CodeGenerator\Models\Traits;

trait HasTraitsDefintions
{

    use HasImportDeclarations;
    use OOPStructComponent;

    /**
     * List of traits
     *
     * @var string[]
     */
    private $traits_;

    public function addTrait(string $trait)
    {
        $this->traits_[] = $trait;
        return $this;
    }
}