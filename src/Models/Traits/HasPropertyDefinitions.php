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

use Drewlabs\CodeGenerator\Contracts\ValueContainer;
use Drewlabs\CodeGenerator\Helpers\Arr;

trait HasPropertyDefinitions
{
    /**
     * @var ValueContainer[]
     */
    private $properties_ = [];

    public function addProperty(ValueContainer $property)
    {
        $properties = [];
        foreach (($this->properties_ ?? []) as $value) {
            $properties[$value->getName()] = $value;
        }
        sort($properties);
        $match = Arr::bsearch(array_keys($properties), $property, static function ($curr, $item) use ($properties) {
            if ($properties[$curr]->equals($item)) {
                return 0;
            }

            return strcmp($properties[$curr]->getName(), $item->getName()) > 0 ? -1 : 1;
        });
        if (-1 !== $match) {
            throw new \RuntimeException('Duplicated property : '.$property->getName());
        }
        $this->properties_[] = $property;

        return $this;
    }

    public function addConstant(ValueContainer $property)
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
