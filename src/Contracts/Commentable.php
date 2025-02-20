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

namespace Drewlabs\CodeGenerator\Contracts;

interface Commentable
{
    /**
     * Add comments to the property definition.
     *
     * @param string[]|string $value
     *
     * @return static|ValueContainer|CallableInterface
     */
    public function addComment($value);
}
