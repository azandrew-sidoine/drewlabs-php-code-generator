<?php

namespace Drewlabs\CodeGenerator\Models;

use Drewlabs\CodeGenerator\Contracts\MethodParamInterface;

/** @package Drewlabs\CodeGenerator\Models */
class MethodParam implements MethodParamInterface
{
    /**
     * Parameter type
     *
     * @var string
     */
    private $type_;

    /**
     * Parameter name
     *
     * @var string
     */
    private $name_;

    /**
     * Parameter default value
     *
     * @var string
     */
    private $default_;


    public function __construct(
        string $name,
        string $type,
        string $default = 'null'
    ) {
        $this->name_ = $name;
        $this->type_ = $type;
        $this->default_ = $default;
    }

    /**
     * Returns the parameter name
     *
     * @return string
     */
    public function name()
    {
        return $this->name_;
    }

    /**
     * Returns the parameter type name
     *
     * @return string
     */
    public function type()
    {
        return $this->type_;
    }

    /**
     * Returns the parameter default value
     *
     * @return string
     */
    public function defaultValue()
    {
        return $this->default_;
    }
}
