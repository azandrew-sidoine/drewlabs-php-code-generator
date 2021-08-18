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

use Drewlabs\CodeGenerator\Contracts\ClassMemberInterface;
use Drewlabs\CodeGenerator\Contracts\CallableInterface;
use Drewlabs\CodeGenerator\Contracts\ValueContainer;

trait OOPStructComponentMembers
{
    /**
     * @var string
     */
    private $name_;

    /**
     * @var string
     */
    private $accessModifier_;

    /**
     * @var string[]|string
     */
    private $descriptors_;

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

    /**
     * {@inheritDoc}
     * 
     * @return ValueContainer|CallableInterface
     */
    public function addComment($value)
    {
        if (null === $value) {
            return $this->descriptors_;
        }
        $this->descriptors_ = \is_array($value) ? $value : [$value];

        return $this;
    }

    public function getName()
    {
        return $this->name_;
    }
}
