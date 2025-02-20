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

trait HasIndentation
{
    /** @var string */
    private $indentation;

    /**
     * @return self
     */
    public function setIndentation(string $indentation)
    {
        $this->indentation = $indentation;

        return $this;
    }

    public function getIndentation()
    {
        return $this->indentation;
    }
}
