<?php

namespace Drewlabs\CodeGenerator;

class ParseClassPathResult
{
    /**
     * Component name
     *
     * @var string
     */
    private $name_;

    /**
     * Class path
     *
     * @var string
     */
    private $classPath_;

    public function __construct(string $name, string $classPath)
    {
        $this->name_ = $name;
        $this->classPath_ = $classPath;
    }

    public function getClassPath()
    {
        return $this->classPath_;
    }

    public function getComponentName()
    {
        return $this->name_;
    }
    


}