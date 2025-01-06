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

namespace Drewlabs\CodeGenerator\Exceptions;

class PHPVariableException extends \Exception
{
    /**
     * Exception instance initializer
     * 
     * @param string $name 
     * @param string $errorMessage 
     */
    public function __construct(string $name, string $errorMessage = '')
    {
        $message = sprintf('Error while building variable %s, %s', $name, $errorMessage);
        parent::__construct($message, 500);
    }
}
