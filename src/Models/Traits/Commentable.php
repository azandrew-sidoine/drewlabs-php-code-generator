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

trait Commentable
{
    /**
     * @var string[]|string
     */
    private $descriptors;

    /**
     * {@inheritDoc}
     *
     * @return mixed
     */
    public function addComment($value)
    {
        if (null === $value) {
            return $this->descriptors;
        }
        $this->descriptors = \is_array($value) ? $value : [$value];
        return $this;
    }

    /**
     * Returns the list of comments on the commentable object.
     *
     * @return string[]
     */
    public function comments()
    {
        // Descriptors must be either array or string
        return array_filter((is_string($this->descriptors) ? [$this->descriptors] : (\is_array($this->descriptors) ? $this->descriptors : [])) ?? []);
    }
}
