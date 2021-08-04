<?php

namespace Drewlabs\CodeGenerator\Models\Traits;

use Drewlabs\CodeGenerator\Contracts\ClassPropertyInterface;
use Drewlabs\Core\Helpers\Arrays\BinarySearchResult;

trait HasPropertyDefinitions
{
    /**
     * @var ClassPropertyInterface[]
     */
    private $properties_ = [];

    public function addProperty(ClassPropertyInterface $property)
    {
        $properties = [];
        foreach (($this->properties_ ?? []) as $value) {
            $properties[$value->getName()] = $value;
        }
        sort($properties);
        $match = drewlabs_core_array_bsearch(array_keys($properties), $property, function($curr, $item) use ($properties) {
            if ($properties[$curr]->equals($item)) {
                return BinarySearchResult::FOUND;
            }
            return strcmp($curr, $item->getName()) > 0 ? BinarySearchResult::LEFT : BinarySearchResult::RIGHT;
        });
        if ($match !== BinarySearchResult::LEFT) {
            throw new \RuntimeException('Duplicated property : ' . $property->getName());
        }
        $this->properties_[] = $property;
        return $this;
    }

    public function addConstant(ClassPropertyInterface $property)
    {
        return $this->addProperty($property->asConstant());
    }
}