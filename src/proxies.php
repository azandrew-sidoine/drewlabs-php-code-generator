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

namespace Drewlabs\CodeGenerator\Proxy;

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
 * It provides a proxy function the {@link PHPClass} constructor.
 *
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
 * Provides a proxy function the {@link PHPClass} constructor.
 *
 * @param string|string[] $descriptors
 *
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
 * Provides a proxy function the {@link PHPClassProperty} constructor.
 *
 * @param mixed|null      $default
 * @param string|string[] $descriptors
 *
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
 * Provides a proxy function the {@link PHPFunctionParameter} constructor.
 *
 * @param mixed|null $default
 *
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
 * Provides  a proxy function the {@link PHPInterface} constructor.
 *
 * @return PHPInterface
 */
function PHPInterface(
    string $name,
    array $methods = []
) {
    return new PHPInterface($name, $methods);
}

/**
 * Provides  a proxy function the {@link PHPNamespace} constructor.
 *
 * @return PHPNamespace
 */
function PHPNamespace(string $namespace)
{
    return new PHPNamespace($namespace);
}

/**
 * Provides  a proxy function the {@link PHPTrait} constructor.
 *
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
 * Provides  a proxy function the {@link CommentModelFactory} constructor.
 *
 * @return CommentModelFactory
 */
function CommentFactory(bool $multiline = true)
{
    return new CommentModelFactory($multiline);
}

/**
 * Provides a proxy function the {@link PHPVariable} constructor.
 *
 * @param mixed|null      $default
 * @param string|string[] $descriptors
 *
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
