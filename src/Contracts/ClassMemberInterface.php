<?php

namespace Drewlabs\CodeGenerator\Contracts;

/** @package Drewlabs\CodeGenerator\Contracts */
interface ClassMemberInterface extends IndentableComponentInterface, ClassPathImportContainer
{
    /**
     * Set the property Access modifier definition
     *
     * @param string $modifier
     * @return self|ClassPropertyInterface|ClassMethodInterface
     */
    public function setModifier(string $modifier);

    /**
     * Add comments to the property definition
     *
     * @param string[]|string $value
     * @return self|ClassPropertyInterface|ClassMethodInterface|mixed
     */
    public function addComment($value);
}