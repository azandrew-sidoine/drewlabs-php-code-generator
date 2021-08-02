<?php

namespace Drewlabs\CodeGenerator\Models\Traits;

trait HasIndentation
{
    private $indentation_;

    /**
     *
     * @param string $indentation
     * @return self
     */
    public function setIndentation(string $indentation)
    {
        $this->indentation_ = $indentation;
        return $this;
    }

    public function getIndentation()
    {
        return $this->indentation_;
    }
}
