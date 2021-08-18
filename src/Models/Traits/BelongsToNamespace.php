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

trait BelongsToNamespace
{
    /**
     * The namespace the class belongs to.
     *
     * @var string
     */
    private $namespace_;

    /**
     * Returns the namespace that the current class belongs to.
     */
    public function getNamespace(): ?string
    {
        return $this->namespace_ ?? null;
    }

    public function addToNamespace(?string $namespace = null)
    {
        if (null !== $namespace) {
            $this->namespace_ = $namespace;
        }

        return $this;
    }
}
