<?php

namespace Drewlabs\CodeGenerator\Contracts;

interface ClassMethodInterface extends ClassMemberInterface
{
    /**
     * Add a new Parameter to the method
     *
     * @param FunctionParameterInterface $param
     * @return self
     */
    public function addParam(FunctionParameterInterface $param);

    /**
     * Specify the exceptions that the current method throws
     *
     * @param array $exceptions
     * @return self
     */
    public function throws($exceptions = []);

    /**
     * Indicates to generate the class as an interface method definitions
     *
     * @return self
     */
    public function asInterfaceMethod();

    /**
     * Add contents to the generated method
     *
     * @param string $content
     * @return self
     */
    public function addContents(string $content);

    /**
     * Checks if two methods definitions are same
     *
     * @param self $value
     * @return bool
     */
    public function equals(self $value);
}
