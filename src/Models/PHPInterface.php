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

use Drewlabs\CodeGenerator\Contracts\ImplementableStruct;
use Drewlabs\CodeGenerator\Converters\PHPInterfaceConverter;
use Drewlabs\CodeGenerator\Helpers\Str;
use Drewlabs\CodeGenerator\Models\Traits\OOPStructComponent;

final class PHPInterface implements ImplementableStruct
{
    use OOPStructComponent;

    /**
     * Class base class name.
     *
     * @var string[]
     */
    private $extends = [];

    /**
     * @var string
     */
    private $preparedExtends;

    public function __construct(
        string $name,
        array $methods = []
    ) {
        $this->name_ = $name;
        // Validate and add user provided methods
        if (null !== $methods && \is_array($methods)) {
            foreach ($methods as $value) {
                // code...
                if (!($value instanceof PHPClassMethod)) {
                    throw new \InvalidArgumentException(sprintf('%s is not an istance of %s', \get_class($value), PHPClassMethod::class));
                }
                $this->addMethod($value);
            }
        }
    }

    public function __toString(): string
    {
        return (new PHPInterfaceConverter())->stringify($this->prepare());
    }

    public function addBaseInterface(string $value)
    {
        if (null !== $value) {
            $this->extends[] = $value;
        }

        return $this;
    }

    public function setBaseInterface(string $value): ImplementableStruct
    {
        return $this->addBaseInterface($value);
    }

    public function getBaseInterface(): ?string
    {
        return $this->preparedExtends;
    }

    public function prepare()
    {
        // Set base class imports

        $extends = [];

        foreach ($this->extends as $extend) {
            if (Str::contains($extend, '\\')) {
                $extend = $this->addClassPathToImportsPropertyAfter(function ($classPath) {
                    return $this->getClassFromClassPath($classPath);
                })($extend);
            }
            $this->setGlobalImports($this->getImports());

            // Add the extended interface to the list of interfaces
            $extends[] = $extend;
        }

        // Join the extended interface to a string value that will be returned when the getBaseInterface is called
        $this->preparedExtends = implode(', ', $extends);

        return $this;
    }
}
