<?php

namespace Drewlabs\CodeGenerator\Models\Traits;

trait HasClassMemberDefinitions
{

    /**
     * @var string
     */
    private $name_;

    /**
     *
     * @var string
     */
    private $accessModifier_;

    /**
     * @var string[]|string
     */
    private $descriptors_;

    public function setModifier(string $value)
    {
        if (null === $value) {
            return $this->accessModifier_;
        }
        $this->accessModifier_ = $value;
        return $this;
    }

    public function addComment($value)
    {
        if (null === $value) {
            return $this->descriptors_;
        }
        $this->descriptors_ = is_array($value) ? $value : [$value];
        return $this;
    }

    public function getName()
    {
        return $this->name_;
    }
}
