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

trait HasPHP8Attributes
{
    /** @var string[] */
    private $php8Attributes = [];

    public function addAttribute(string $attribute)
    {
        $this->php8Attributes[] = $attribute;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->php8Attributes ?? [];
    }
}