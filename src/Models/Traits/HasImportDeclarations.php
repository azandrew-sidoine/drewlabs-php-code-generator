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
use Drewlabs\CodeGenerator\Helpers\Str;
use Drewlabs\CodeGenerator\ParseClassPathResult;

trait HasImportDeclarations
{
    /** @var string[] List of imports to append to the file/class imports. */
    private $imports;

    /** @var string[] */
    private $globalImports;

    /**
     * {@inheritDoc}
     */
    public function getImports(): array
    {
        return $this->imports ?? [];
    }

    /**
     * {@inheritDoc}
     */
    public function setGlobalImports(array $values)
    {
        $this->globalImports = $values;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getGlobalImports(): array
    {
        return $this->globalImports ?? [];
    }

    private function addClassPathToImportsPropertyAfter(\Closure $callback)
    {
        return function (string $value) use ($callback) {
            $result = $callback($value);
            $name = $result instanceof ParseClassPathResult ? $result->getComponentName() : $result;
            $classPath = $result instanceof ParseClassPathResult ? $result->getClassPath() : $value;
            $imports = $this->imports ?? [];
            if (!\in_array($value, $imports, true) && (null !== $classPath)) {
                $this->imports[] = ltrim($classPath, '\\');
            }

            return $name;
        };
    }

    private function getClassFromClassPath(string $classPath)
    {
        // If the classPath is not a class path, return the ParseClassPathResult with the name == $classPath
        if (!Str::contains($classPath, '\\')) {
            return new ParseClassPathResult($classPath);
        }
        // Get the global imports components
        $globalImports = $this->getGlobalImports() ?? [];
        $components = array_reverse(Str::split($classPath, '\\'));
        // Get the class name from the class path
        $name = $components[0];
        // Get the namespace of the component
        $namespace = null !== ($namespace = ($this instanceof NamespaceComponent) ? $this->getNamespace() : null) ? rtrim($namespace, '\\') : null;

        // Do not add the class path to the imports statement if the last item of the class path is in the same
        if ($namespace && Str::contains($classPath, $namespace) && !Str::contains(str_replace($namespace.'\\', '', $classPath), '\\')) {
            return new ParseClassPathResult($name);
        } elseif (!\in_array(trim(ltrim($classPath, "\\")), $globalImports ?? [], true)) {
            $matches = array_filter($globalImports ?? [], static function ($import) use ($name) {
                return \is_string($import) && Str::endsWith($import, $name);
            });
            if (!empty($matches)) {
                $prefix = \count($components) > 1 ? $components[1] : 'Base';
                $name = Str::camelize(sprintf('%s%s', $prefix, $name));
                $classPath = sprintf('%s as %s', $classPath, $name);
            }

            return new ParseClassPathResult($name, $classPath);
        }

        return new ParseClassPathResult($name);
    }
}
