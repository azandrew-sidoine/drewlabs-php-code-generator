<?php

declare(strict_types=1);

/*
 * This file is part of the Drewlabs package.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        $match = drewlabs_core_array_bsearch(array_keys($properties), $property, static function ($curr, $item) use ($properties) {
            if ($properties[$curr]->equals($item)) {
                return BinarySearchResult::FOUND;
            }

            return strcmp($properties[$curr]->getName(), $item->getName()) > 0 ? BinarySearchResult::LEFT : BinarySearchResult::RIGHT;
        });
        if (BinarySearchResult::LEFT !== $match) {
            throw new \RuntimeException('Duplicated property : '.$property->getName());
        }
        $this->properties_[] = $property;

        return $this;
    }

    public function addConstant(ClassPropertyInterface $property)
    {
        return $this->addProperty($property->asConstant());
    }

    // Add members getters

    /**
     * {@inheritDoc}
     */
    public function getProperties(): array
    {
        return $this->properties_ ?? [];
    }
}
