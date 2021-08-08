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
    private $interfaces_ = [];

    /**
     * Class base class name.
     *
     * @var string
     */
    private $baseClass_;

    /**
     * Create an abstract php class.
     *
     * @var bool
     */
    private $isAbstract_;

    /**
     * Create a final php class.
     *
     * @var bool
     */
    private $isFinal_;

    public function setBaseClass(string $baseClass)
    {
        if (null !== $baseClass) {
            $this->baseClass_ = $baseClass;
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
            $this->interfaces_[] = $value;
        }

        return $this;
    }

    public function asFinal()
    {
        if ($this->isAbstract_) {
            throw new PHPClassException('Class cannot be final and abstract at the same time');
        }
        $this->isFinal_ = true;

        return $this;
    }

    public function asAbstract()
    {
        if ($this->isFinal_) {
            throw new PHPClassException('Class cannot be final and abstract at the same time');
        }
        $this->isAbstract_ = true;

        return $this;
    }

    /**
     * Returns the list of interfaces that the blueprint implements.
     *
     * @return string[]
     */
    public function getImplementations(): ?array
    {
        return $this->interfaces_ ?? [];
    }

    /**
     * Returns the base class the blueprint definition.
     */
    public function getBaseClass(): ?string
    {
        return $this->baseClass_ ?? null;
    }

    /**
     * Checks if the blueprint definition is a final blueprint definition.
     */
    public function isFinal(): bool
    {
        return $this->isFinal_ ?? false;
    }

    /**
     * Checks if the blueprint definition is an abstract blueprint definition.
     */
    public function isAbstract(): bool
    {
        return $this->isAbstract_ ?? false;
    }
}
