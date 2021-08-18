<?php


namespace Drewlabs\CodeGenerator\Contracts;

interface Commentable
{
    /**
     * Add comments to the property definition.
     *
     * @param string[]|string $value
     *
     * @return self|ValueContainer|CallableInterface|mixed
     */
    public function addComment($value);
}
