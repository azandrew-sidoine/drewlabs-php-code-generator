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

use Drewlabs\CodeGenerator\Contracts\HasVisibility;
use Drewlabs\CodeGenerator\Types\PHPTypesModifiers;

class PHPConstructorParameter extends PHPFunctionParameter implements HasVisibility
{
    /** @var string */
    private $visibility = PHPTypesModifiers::PRIVATE;

    /**
     * Set the param visibility to the provided value
     * 
     * @param string $value 
     * @return static 
     */
    public function setVisibility(string $value)
    {
        $this->visibility = $value;

        return $this;
    }

    public function getVisibility(): ?string
    {
        return $this->visibility;
    }
}
