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

use Drewlabs\CodeGenerator\Contracts\Blueprint;
use Drewlabs\CodeGenerator\Contracts\CallableInterface;
use Drewlabs\CodeGenerator\Contracts\ClassPropertyInterface;
use Drewlabs\CodeGenerator\Converters\PHPClassConverter;
use Drewlabs\CodeGenerator\Models\Traits\OOPBlueprintComponent;

final class PHPClass implements Blueprint
{
    use OOPBlueprintComponent;

    /**
     * Undocumented function.
     *
     * @param string                   $implementations
     * @param CallableInterface[]   $methods
     * @param ClassPropertyInterface[] $properties
     */
    public function __construct(
        string $name,
        array $implementations = [],
        array $methods = [],
        array $properties = []
    ) {
        $this->name_ = $name;
        // Validate implementations
        if (drewlabs_core_array_is_arrayable($implementations)) {
            foreach ($implementations as $value) {
                // code...
                if (!drewlabs_core_strings_is_str($value)) {
                    throw new \InvalidArgumentException(sprintf('%s is not an istance of PHP string', \get_class($value)));
                }
                $this->addImplementation($value);
            }
        }
        // Validate methods
        if (null !== $methods && \is_array($methods)) {
            foreach ($methods as $value) {
                // code...
                if (!($value instanceof CallableInterface)) {
                    throw new \InvalidArgumentException(sprintf('%s is not an istance of %s', \get_class($value), CallableInterface::class));
                }
                $this->addMethod($value);
            }
        }

        // Validate and add properties properties
        if (null !== $properties && \is_array($methods)) {
            foreach ($properties as $value) {
                // code...
                if (!($value instanceof ClassPropertyInterface)) {
                    throw new \InvalidArgumentException(sprintf('%s is not an istance of %s', \get_class($value), ClassPropertyInterface::class));
                }
                $this->addProperty($value);
            }
        }
    }

    public function __toString(): string
    {
        return (new PHPClassConverter())->stringify($this->setImports());
    }

    /**
     * Adds a constant property definition to the class.
     *
     * @return self
     */
    public function addConstant(ClassPropertyInterface $property)
    {
        return $this->addProperty($property->asConstant());
    }

    /**
     * Set the class imports and returns.
     *
     * @return self
     */
    public function setImports()
    {
        // Loop through interfaces
        $interfaces = [];
        foreach (($this->interfaces_ ?? []) as $value) {
            if (drewlabs_core_strings_contains($value, '\\')) {
                $interfaces[] = $this->addClassPathToImportsPropertyAfter(function ($classPath) {
                    return $this->getClassFromClassPath($classPath);
                })($value);
            } else {
                $interfaces[] = $value;
            }
        }
        $this->interfaces_ = $interfaces;

        // Set base class imports
        if (drewlabs_core_strings_contains($this->baseClass_, '\\')) {
            $this->baseClass_ = $this->addClassPathToImportsPropertyAfter(function ($classPath) {
                return $this->getClassFromClassPath($classPath);
            })($this->baseClass_);
        }

        return $this;
    }
}
