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
use Drewlabs\CodeGenerator\Contracts\HasPHP8Attributes as AbstractHasPHP8Attributes;
use Drewlabs\CodeGenerator\Contracts\ValueContainer;
use Drewlabs\CodeGenerator\Converters\PHPClassStringifier;
use Drewlabs\CodeGenerator\Helpers\Str;
use Drewlabs\CodeGenerator\Models\Traits\HasPHP8Attributes;
use Drewlabs\CodeGenerator\Models\Traits\OOPBlueprintComponent;
use Drewlabs\CodeGenerator\Types\PHPTypesModifiers;

final class PHPClass implements Blueprint, AbstractHasPHP8Attributes
{
    use OOPBlueprintComponent;
    use HasPHP8Attributes;

    /** @var bool */
    private $promoteProperties = false;

    /** @var  PHPClassPropertyHook[] */
    private $propertyHooks = [];

    /** @var CallableInterface */
    private $constructor;

    /**
     * Class constructor
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
        $this->name = $name;
        // Validate implementations
        if (is_iterable($implementations)) {
            foreach ($implementations as $value) {
                // code...
                if (!\is_string($value)) {
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

    /**
     * Build blueprint with PHP 8+ promoted properties syntax support
     * 
     * @return static 
     */
    public function withPromotedProperties()
    {
        $this->promoteProperties = true;
        return $this;
    }

    public function __toString(): string
    {
        return (new PHPClassStringifier($this->promoteProperties))->stringify($this->prepare());
    }

    public function addConstructor(array $params = [], array $lines = [], $modifier = PHPTypesModifiers::PUBLIC)
    {
        $method = new PHPClassMethod(
            '__construct',
            $params ?? [],
            null,
            $modifier ?? PHPTypesModifiers::PUBLIC,
            'Class instance initializer'
        );

        foreach (
            array_filter($lines ?? [], function ($line) {
                return null !== $line;
            }) as $line
        ) {
            $method = $method->addLine($line);
        }

        $this->constructor = $method;

        return $this;
    }

    public function getConstructor(): ?CallableInterface
    {
        return $this->constructor;
    }

    public function asInvokable()
    {
        return $this->addMethod(
            new PHPClassMethod(
                '__invoke',
                [],
                'void',
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
        if ((null !== $classPath) && Str::contains($classPath, '\\')) {
            $this->addClassPathToImportsPropertyAfter(function ($path) {
                return $this->getClassFromClassPath($path);
            })($classPath);
        }

        return $this;
    }

    public function addFunctionPath(string $value)
    {
        $imports = $this->imports ?? [];
        if (!empty($value) && !\in_array($value, $imports, true) && (null !== $value)) {
            $this->imports[] = sprintf('function %s', ltrim($value, '\\'));
        }

        return $this;
    }

    /**
     * Set the class imports and returns.
     *
     * @return static
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
        // Loop through interfaces
        $interfaces = [];
        foreach (($this->interfaces ?? []) as $value) {
            if (Str::contains($value, '\\')) {
                $interfaces[] = $this->addClassPathToImportsPropertyAfter(function ($classPath) {
                    return $this->getClassFromClassPath($classPath);
                })($value);
                $this->setGlobalImports($this->getImports());
            } else {
                $interfaces[] = $value;
            }
        }
        $this->interfaces = $interfaces;

        // Set base class imports
        if (Str::contains($this->baseClass, '\\')) {
            $this->baseClass = $this->addClassPathToImportsPropertyAfter(function ($classPath) {
                return $this->getClassFromClassPath($classPath);
            })($this->baseClass);
            $this->setGlobalImports($this->getImports());
        }

        return $this;
    }
}
