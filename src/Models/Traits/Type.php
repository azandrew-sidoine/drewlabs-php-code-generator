<?php

namespace Drewlabs\CodeGenerator\Models\Traits;

trait Type
{
    use Commentable;

    /**
     * @var string
     */
    private $name_;

    /**
     * @var string
     */
    private $type_;

    public function setName(?string $value = null)
    {
        if ((null !== $value) && is_string($value)) {
            $this->name_ = $value;
        }
        return $this;
    }

    public function getName()
    {
        return $this->name();
    }

    public function name(): ?string
    {
        return $this->name_;
    }

    public function setType(?string $value = null)
    {
        if ((null !== $value )&& (is_string($value))) {
            $this->type_ = $value;
        }
        return $this;
    }

    /**
     * Returns the parameter type name.
     *
     * @return string
     */
    public function type()
    {
        return $this->type_;
    }
}
