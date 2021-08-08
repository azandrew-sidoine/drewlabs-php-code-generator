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

interface OOPComposableStruct extends OOPStructInterface
{
    /**
     * Returns the list of traits of the blueprint.
     *
     * @return string[]
     */
    public function getTraits(): ?array;

    /**
     * Add a given trait to the structure definition.
     *
     * @return self|mixed
     */
    public function addTrait(string $trait);
}
