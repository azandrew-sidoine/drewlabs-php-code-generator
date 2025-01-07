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

trait Type
{
    use Commentable;

    /** @var string */
    private $name;

    /** @var string */
    private $type;

    public function setName(?string $value = null)
    {
        if ((null !== $value) && \is_string($value)) {
            $this->name = $value;
        }

        return $this;
    }

    public function getName()
    {
        return $this->name();
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function setType(?string $value = null)
    {
        if ((null !== $value) && (\is_string($value))) {
            $this->type = $value;
        }

        return $this;
    }

    /**
     * Returns the parameter type name.
     * @deprecated
     *
     * @return string
     */
    public function type()
    {
        return $this->type;
    }


    public function getType(): ?string
    {
        return $this->type;
    }
}
