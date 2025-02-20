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

namespace Drewlabs\CodeGenerator\Models;

use Drewlabs\CodeGenerator\Contracts\TraitableStruct;
use Drewlabs\CodeGenerator\Converters\PHPTraitStringifier;
use Drewlabs\CodeGenerator\Helpers\Str;
use Drewlabs\CodeGenerator\Models\Traits\HasTraitsDefintions;

final class PHPTrait implements TraitableStruct
{
    use HasTraitsDefintions;

    public function __construct(
        string $name,
        array $methods = [],
        array $properties = []
    ) {
        $this->name = $name;
        // Validate methods
        if (null !== $methods && \is_array($methods)) {
            foreach ($methods as $value) {
                // code...
                if (!($value instanceof PHPClassMethod)) {
                    throw new \InvalidArgumentException(sprintf('%s is not an istance of %s', \get_class($value), PHPClassMethod::class));
                }
                $this->addMethod($value);
            }
        }

        // Validate and add properties properties
        if (null !== $properties && \is_array($methods)) {
            foreach ($properties as $value) {
                // code...
                if (!($value instanceof PHPClassProperty)) {
                    throw new \InvalidArgumentException(sprintf('%s is not an istance of %s', \get_class($value), PHPClassProperty::class));
                }
                $this->addProperty($value);
            }
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    public function __toString(): string
    {
        return (new PHPTraitStringifier())->stringify($this->prepare()) ?? '';
    }

    /**
     * Set the class imports and returns.
     *
     * @return self
     */
    public function prepare()
    {
        $traits = [];
        foreach (($this->traits ?? []) as $value) {
            if (Str::contains($value, '\\')) {
                $traits[] = $this->addClassPathToImportsPropertyAfter(function ($classPath) {
                    return $this->getClassFromClassPath($classPath);
                })($value);
                $this->setGlobalImports($this->getImports());
            } else {
                $traits[] = $value;
            }
        }
        $this->traits = $traits;

        return $this;
    }
}
