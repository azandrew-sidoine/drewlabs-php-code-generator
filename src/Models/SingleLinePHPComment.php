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

use Drewlabs\CodeGenerator\Contracts\Stringable;

class SingleLinePHPComment implements Stringable
{
    /**
     * Private comment description property.
     *
     * @var string
     */
    private $description;

    /**
     * Class instance initializer
     * 
     * @param string $description 
     */
    public function __construct(string $description)
    {
        $this->description = $description;
    }

    public function __toString(): string
    {
        return sprintf('// %s', $this->description);
    }
}
