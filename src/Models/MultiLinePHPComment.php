<?php

namespace Drewlabs\CodeGenerator\Models;

use Drewlabs\CodeGenerator\Contracts\Stringable;
use Drewlabs\CodeGenerator\Models\Traits\HasIndentation;

class MultiLinePHPComment implements Stringable
{
    use HasIndentation;

    /**
     * List of descriptions 
     *
     * @var array
     */
    private $descriptors_;

    public function __construct(array $descriptors = [])
    {
        $this->descriptors_ = $descriptors;
    }

    /**
     * Set the comment descriptors that will compose the comment
     *
     * @param array $descriptors
     * @return self
     */
    public function setDescriptors(array $descriptors)
    {
        $this->descriptors_ = $descriptors;
        return $this;
    }

    /**
     * Returns the list of descriptors define on the comment
     *
     * @return array
     */
    public function getDescriptors()
    {
        return $this->descriptors_;
    }

    public function __toString(): string
    {
        $start = "/**";
        $parts[0] = $start;
        foreach (($this->getDescriptors() ?? []) as $key => $value) {
            if ($key === 1) {
                $parts[] =  $this->getIndentation() ? $this->getIndentation() . " *" : " *";
            }
            $parts[] = $this->getIndentation() ? sprintf("%s * %s", $this->getIndentation(), $value) : sprintf(" * %s", $value);
        }
        $parts[] = $this->getIndentation() ? sprintf("%s */", $this->getIndentation()) : " */";
        return implode(PHP_EOL, $parts);
    }
}
