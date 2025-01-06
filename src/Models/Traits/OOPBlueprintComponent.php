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

use Drewlabs\CodeGenerator\Exceptions\PHPClassException;

trait OOPBlueprintComponent
{
    use HasTraitsDefintions;

    /**
     * @var string[]
     */
    private $interfaces = [];

    /**
     * Class base class name.
     *
     * @var string
     */
    private $baseClass;

    /**
     * Create an abstract php class.
     *
     * @var bool
     */
    private $isAbstract;

    /**
     * Create a final php class.
     *
     * @var bool
     */
    private $isFinal;

    public function setBaseClass(string $baseClass)
    {
        if (null !== $baseClass) {
            $this->baseClass = $baseClass;
        }

        return $this;
    }

    /**
     * Add an interface or an implementation to the class.
     *
     * @return self
     */
    public function addImplementation(string $value)
    {
        if (null !== $value) {
            $this->interfaces[] = $value;
        }

        return $this;
    }

    public function asFinal()
    {
        if ($this->isAbstract) {
            throw new PHPClassException('Class cannot be final and abstract at the same time');
        }
        $this->isFinal = true;

        return $this;
    }

    public function asAbstract()
    {
        if ($this->isFinal) {
            throw new PHPClassException('Class cannot be final and abstract at the same time');
        }
        $this->isAbstract = true;

        return $this;
    }

    /**
     * Returns the list of interfaces that the blueprint implements.
     *
     * @return string[]
     */
    public function getImplementations(): ?array
    {
        return $this->interfaces ?? [];
    }

    /**
     * Returns the base class the blueprint definition.
     */
    public function getBaseClass(): ?string
    {
        return $this->baseClass ?? null;
    }

    /**
     * Checks if the blueprint definition is a final blueprint definition.
     */
    public function isFinal(): bool
    {
        return $this->isFinal ?? false;
    }

    /**
     * Checks if the blueprint definition is an abstract blueprint definition.
     */
    public function isAbstract(): bool
    {
        return $this->isAbstract ?? false;
    }
}
