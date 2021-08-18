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

namespace Drewlabs\CodeGenerator\Models\Traits;

use Drewlabs\CodeGenerator\Contracts\NamespaceComponent;
use Drewlabs\CodeGenerator\ParseClassPathResult;

trait HasImportDeclarations
{
    /**
     * List of imports to append to the file/class imports.
     *
     * @var string[]
     */
    private $imports_;

    /**
     * Undocumented variable.
     *
     * @var string[]
     */
    private $globalImports_;

    /**
     * {@inheritDoc}
     */
    public function getImports(): array
    {
        return $this->imports_ ?? [];
    }

    /**
     * {@inheritDoc}
     */
    public function setGlobalImports(array $values)
    {
        $this->globalImports_ = $values;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getGlobalImports(): array
    {
        return $this->globalImports_ ?? [];
    }

    private function addClassPathToImportsPropertyAfter(\Closure $callback)
    {
        return function (string $value) use ($callback) {
            $result = $callback($value);
            $name = $result instanceof ParseClassPathResult ? $result->getComponentName() : $result;
            $classPath = $result instanceof ParseClassPathResult ? $result->getClassPath() : $value;
            $this->imports_ = $this->imports_ ?? [];
            if (!\in_array($value, $this->imports_, true) && (null !== $classPath)) {
                $this->imports_[] = ltrim($classPath, "\\");
            }
            return $name;
        };
    }

    private function getClassFromClassPath(string $classPath)
    {
        // If the classPath is not a class path, return the ParseClassPathResult with the name == $classPath
        if (!drewlabs_core_strings_contains($classPath, "\\")) {
            return new ParseClassPathResult($classPath);
        }
        // Get the global imports components
        $globalImports_ = $this->getGlobalImports() ?? [];
        $classPathComponents = array_reverse(drewlabs_core_strings_to_array($classPath, '\\'));
        // Get the class name from the class path
        $name = $classPathComponents[0];
        // Get the namespace of the component
        $namespace = ($this instanceof NamespaceComponent) ? rtrim($this->getNamespace(), "\\") : null;
        // Do not add the class path to the imports statement if the last item of the class path is in the same 
        if ($namespace && drewlabs_core_strings_contains($classPath, $namespace) && !drewlabs_core_strings_contains(drewlabs_core_strings_replace($namespace . "\\", "", $classPath), "\\")) {
            return new ParseClassPathResult($name);
        } else if (!\in_array($classPath, $globalImports_ ?? [], true)) {
            $matches = array_filter($globalImports_ ?? [], static function ($import) use ($name) {
                return \is_string($import) && drewlabs_core_strings_ends_with($import, $name);
            });
            if (!empty($matches)) {
                $prefix = \count($classPathComponents) > 1 ? $classPathComponents[1] : 'Base';
                $name = drewlabs_core_strings_as_camel_case(sprintf('%s%s', $prefix, $name));
                $classPath = sprintf('%s as %s', $classPath, $name);
            }

            return new ParseClassPathResult($name, $classPath);
        }

        return new ParseClassPathResult($name);
    }
}
