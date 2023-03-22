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

use Drewlabs\CodeGenerator\Contracts\CallableInterface;
use Drewlabs\CodeGenerator\Contracts\ValueContainer;

trait OOPStructComponentMembers
{
    /**
     * @var string
     */
    private $accessModifier_;

    /**
     * {@inheritDoc}
     *
     * @return ValueContainer|CallableInterface
     */
    public function setModifier(string $value)
    {
        if (null === $value) {
            return $this->accessModifier_;
        }
        $this->accessModifier_ = $value;

        return $this;
    }

    public function accessModifier()
    {
        return $this->accessModifier_;
    }
}
