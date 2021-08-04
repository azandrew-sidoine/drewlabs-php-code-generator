<?php

namespace Drewlabs\CodeGenerator\Contracts;


/** @package Drewlabs\CodeGenerator\Contracts */
interface ClassPropertyInterface extends ClassMemberInterface
{
    /**
     * Type property setter
     *
     * @param string $type
     * @return self
     */
    public function setType(string $type);

    /**
     * Set the property default value definition or returns it it null is passed
     *
     * @param array|string $value
     * @return mixed|self
     */
    public function value($value = null);

    /**
     * Create the property as PHP Constant
     *
     * @return self|ClassPropertyInterface
     */
    public function asConstant();

    /**
     * Checks if two properties are same
     *
     * @param self $value
     * @return bool
     */
    public function equals(self $value);
}