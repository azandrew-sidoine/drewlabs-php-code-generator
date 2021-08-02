<?php

namespace Drewlabs\CodeGenerator\Models;

use Drewlabs\CodeGenerator\Contracts\Stringable;

class SingleLinePHPComment implements Stringable
{
    /**
     * Private comment description property
     *
     * @var string
     */
    private $description_;

    public function __construct(string $description)
    {
        $this->description_ = $description;
    }

    public function __toString(): string
    {
        return sprintf("// %s", $this->description_);
    }

}