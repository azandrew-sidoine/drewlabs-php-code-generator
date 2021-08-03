<?php

namespace Drewlabs\CodeGenerator\Models\Traits;

use Drewlabs\CodeGenerator\Models\PHPClassMethod;
use Drewlabs\CodeGenerator\Models\PHPClassProperty;

trait OOPComponent
{
    use HasImportDeclarations;

    /**
     * @var string
     */
    private $name_;
    /**
     * @var PHPClassMethod[]
     */
    private $methods_ = [];
    /**
     * @var PHPClassProperty[]
     */
    private $properties_ = [];

    /**
     * The namespace the class belongs to
     *
     * @var string
     */
    private $namespace_;


    public function getName()
    {
        return $this->name_;
    }

    public function addProperty(PHPClassProperty $property)
    {
        $this->properties_[] = $property;
        return $this;
    }

    public function addMethod(PHPClassMethod $property)
    {
        $this->methods_[] = $property;
        return $this;
    }

    public function addToNamespace(string $namespace)
    {
        $this->namespace_ = $namespace;
        return $this;
    }

}