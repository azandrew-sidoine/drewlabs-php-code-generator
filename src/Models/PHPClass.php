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
use Drewlabs\CodeGenerator\Contracts\ValueContainer;
use Drewlabs\CodeGenerator\Converters\PHPClassConverter;
use Drewlabs\CodeGenerator\Models\Traits\OOPBlueprintComponent;
use Drewlabs\CodeGenerator\Types\PHPTypesModifiers;

final class PHPClass implements Blueprint
{
    use OOPBlueprintComponent;

    /**
     * Undocumented function.
     *
     * @param string              $implementations
     * @param CallableInterface[] $methods
     * @param ValueContainer[]    $properties
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
                if (!($value instanceof ValueContainer)) {
                    throw new \InvalidArgumentException(sprintf('%s is not an istance of %s', \get_class($value), ValueContainer::class));
                }
                $this->addProperty($value);
            }
        }
    }

    public function __toString(): string
    {
        return (new PHPClassConverter())->stringify($this->prepare());
    }

    public function addConstructor(array $params = [], array $lines = [], $modifier = PHPTypesModifiers::PUBLIC)
    {
        $method = new PHPClassMethod(
            '__construct',
            $params ?? [],
            'self',
            $modifier ?? PHPTypesModifiers::PUBLIC,
            'Class instance initializer'
        );
        foreach (array_filter($lines ?? [], function ($line) {
            return null !== $line;
        }) as $line) {
            $method = $method->addLine($line);
        }
        return $this->addMethod($method);
    }

    public function asInvokable()
    {
        return $this->addMethod(
            new PHPClassMethod(
                '__invoke',
                [],
                'self',
                PHPTypesModifiers::PUBLIC,
                'Undocumented method'
            )
        );
    }

    public function asStringable()
    {
        return $this->addMethod(
            new PHPClassMethod(
                '__toString',
                [],
                'string',
                PHPTypesModifiers::PUBLIC,
                'Convert the instances of the current class to PHP string'
            )
        );
    }

    public function addConstant(ValueContainer $property)
    {
        return $this->addProperty($property->asConstant());
    }

    public function addClassPath(string $classPath)
    {
        if ((null !== $classPath) && drewlabs_core_strings_contains($classPath, '\\')) {
            $this->addClassPathToImportsPropertyAfter(function ($path) {
                return $this->getClassFromClassPath($path);
            })($classPath);
        }

        return $this;
    }

    public function addFunctionPath(string $value)
    {
        $imports = $this->imports_ ?? [];
        if (!empty($value) && !\in_array($value, $imports, true) && (null !== $value)) {
            $this->imports_[] = sprintf('function %s', ltrim($value, '\\'));
        }

        return $this;
    }

    /**
     * Set the class imports and returns.
     *
     * @return self
     */
    public function prepare()
    {
        $traits = [];
        foreach (($this->traits_ ?? []) as $value) {
            if (drewlabs_core_strings_contains($value, '\\')) {
                $traits[] = $this->addClassPathToImportsPropertyAfter(function ($classPath) {
                    return $this->getClassFromClassPath($classPath);
                })($value);
                $this->setGlobalImports($this->getImports());
            } else {
                $traits[] = $value;
            }
        }
        $this->traits_ = $traits;
        // Loop through interfaces
        $interfaces = [];
        foreach (($this->interfaces_ ?? []) as $value) {
            if (drewlabs_core_strings_contains($value, '\\')) {
                $interfaces[] = $this->addClassPathToImportsPropertyAfter(function ($classPath) {
                    return $this->getClassFromClassPath($classPath);
                })($value);
                $this->setGlobalImports($this->getImports());
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
            $this->setGlobalImports($this->getImports());
        }

        return $this;
    }
}
