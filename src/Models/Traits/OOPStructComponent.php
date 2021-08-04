<?php

namespace Drewlabs\CodeGenerator\Models\Traits;

use Drewlabs\CodeGenerator\Contracts\ClassPropertyInterface;
use Drewlabs\CodeGenerator\Contracts\ClassMethodInterface;
use Drewlabs\Core\Helpers\Arrays\BinarySearchResult;

trait OOPStructComponent
{
    use HasImportDeclarations;
    use HasPropertyDefinitions;

    /**
     * @var string
     */
    private $name_;
    /**
     * @var ClassMethodInterface[]
     */
    private $methods_ = [];

    /**
     * The namespace the class belongs to
     *
     * @var string
     */
    private $namespace_;


    public function getName()
    {
        return $this->name_;
    }

    public function addMethod(ClassMethodInterface $method)
    {
        $methods = [];
        foreach (($this->methods_ ?? []) as $value) {
            $methods[$value->getName()] = $value;
        }
        sort($methods);
        $match = drewlabs_core_array_bsearch(array_keys($methods), $method, function($curr,  ClassMethodInterface $item) use ($methods) {
            if ($methods[$curr]->equals($item)) {
                return BinarySearchResult::FOUND;
            }
            return strcmp($curr, $item->getName()) > 0 ? BinarySearchResult::LEFT : BinarySearchResult::RIGHT;
        });
        if ($match !== BinarySearchResult::LEFT) {
            throw new \RuntimeException('Duplicated method definition : ' . $method->getName());
        }
        $this->methods_[] = $method;
        return $this;
    }

    public function addToNamespace(string $namespace)
    {
        $this->namespace_ = $namespace;
        return $this;
    }

}