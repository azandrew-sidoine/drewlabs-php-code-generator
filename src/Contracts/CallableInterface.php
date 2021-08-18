<?php

declare(strict_types=1);

/*
 * This file is part of the Drewlabs package.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\CodeGenerator\Contracts;

interface CallableInterface extends Indentable, NamespaceComponent, PathImportContainer, Commentable
{
    /**
     * Add a new Parameter to the method.
     *
     * @return self
     */
    public function addParam(FunctionParameterInterface $param);

    /**
     * Specify the exceptions that the current method throws.
     *
     * @param array $exceptions
     *
     * @return self
     */
    public function throws($exceptions = []);

    /**
     * Indicates to generate the class as an interface method definitions.
     *
     * @return self
     */
    public function asInterfaceMethod();

    /**
     * Add contents to the generated method.
     *
     * @return self
     */
    public function addContents(string $content);

    /**
     * Checks if two methods definitions are same.
     *
     * @return bool
     */
    public function equals(self $value);

    /**
     * Add a new line to the method.
     *
     * @return self
     */
    public function addLine(string $line);

    /**
     * Set the return type of the function or method.
     *
     * @return self
     */
    public function setReturnType(string $type);
}
