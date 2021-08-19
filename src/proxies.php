<?php

namespace Drewlabs\CodeGenerator\Proxy {

    use Drewlabs\CodeGenerator\CommentModelFactory;
    use Drewlabs\CodeGenerator\Models\PHPClass;
    use Drewlabs\CodeGenerator\Models\PHPClassMethod;
    use Drewlabs\CodeGenerator\Models\PHPClassProperty;
    use Drewlabs\CodeGenerator\Models\PHPFunctionParameter;
    use Drewlabs\CodeGenerator\Models\PHPInterface;
    use Drewlabs\CodeGenerator\Models\PHPNamespace;
    use Drewlabs\CodeGenerator\Models\PHPTrait;
    use Drewlabs\CodeGenerator\Models\PHPVariable;

/**
     * It provides a proxy function the {@link PHPClass} constructor
     * 
     * @param string $name 
     * @param array $implementations 
     * @param array $methods 
     * @param array $properties 
     * @return PHPClass
     */
    function PHPClass(
        string $name,
        array $implementations = [],
        array $methods = [],
        array $properties = []
    ) {
        return new PHPClass($name, $implementations, $methods, $properties);
    }

    /**
     * Provides a proxy function the {@link PHPClass} constructor
     * 
     * @param string $name 
     * @param array $params 
     * @param null|string $returns 
     * @param null|string $modifier 
     * @param string|string[] $descriptors 
     * @return PHPClassMethod 
     */
    function PHPClassMethod(
        string $name,
        array $params = [],
        ?string $returns = null,
        ?string $modifier = 'public',
        $descriptors = ''
    ) {
        return new PHPClassMethod($name, $params, $returns, $modifier, $descriptors);
    }

    /**
     * Provides a proxy function the {@link PHPClassProperty} constructor
     * 
     * @param string $name 
     * @param null|string $type 
     * @param null|string $modifier 
     * @param mixed|null $default 
     * @param string|string[] $descriptors 
     * @return PHPClassProperty 
     */
    function PHPClassProperty(
        string $name,
        ?string $type = null,
        ?string $modifier = 'public',
        $default = null,
        $descriptors = ''
    ) {
        return new PHPClassProperty($name, $type, $modifier, $default, $descriptors);
    }

    /**
     * Provides a proxy function the {@link PHPFunctionParameter} constructor
     * 
     * @param string $name 
     * @param null|string $type 
     * @param mixed|null $default 
     * @return PHPFunctionParameter 
     */
    function PHPFunctionParameter(
        string $name,
        ?string $type = null,
        $default = null
    ) {
        return new PHPFunctionParameter($name, $type, $default);
    }

    /**
     * Provides  a proxy function the {@link PHPInterface} constructor
     * 
     * @param string $name 
     * @param array $methods 
     * @return PHPInterface 
     */
    function PHPInterface(
        string $name,
        array $methods = []
    ) {
        return new PHPInterface($name, $methods);
    }

    /**
     * Provides  a proxy function the {@link PHPNamespace} constructor
     * 
     * @param string $namespace 
     * @return PHPNamespace 
     */
    function PHPNamespace(string $namespace)
    {
        return new PHPNamespace($namespace);
    }

    /**
     * Provides  a proxy function the {@link PHPTrait} constructor
     * 
     * @param string $name 
     * @param array $methods 
     * @param array $properties 
     * @return PHPTrait 
     */
    function PHPTrait(
        string $name,
        array $methods = [],
        array $properties = []
    ) {
        return new PHPTrait($name, $methods, $properties);
    }

    /**
     * Provides  a proxy function the {@link CommentModelFactory} constructor
     * 
     * @param bool $multiline 
     * @return CommentModelFactory 
     */
    function CommentFactory(bool $multiline = true)
    {
        return new CommentModelFactory($multiline);
    }

    /**
     * Provides a proxy function the {@link PHPVariable} constructor
     * 
     * @param string $name 
     * @param null|string $type 
     * @param mixed|null $default 
     * @param string|string[] $descriptors 
     * @return PHPVariable 
     */
    function PHPVariable(
        string $name,
        ?string $type = null,
        $default = null,
        $descriptors = ''
    ) {
        return new PHPVariable(
            $name,
            $type,
            $default,
            $descriptors
        );
    }
}
