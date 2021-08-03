<?php

namespace Drewlabs\CodeGenerator\Models\Traits;

use Drewlabs\CodeGenerator\ParseClassPathResult;

trait HasImportDeclarations
{

    /**
     * List of imports to append to the file/class imports
     *
     * @var string[]
     */
    private $imports_;


    /**
     * Undocumented variable
     *
     * @var string[]
     */
    private $globalImports_;

    public function getImports(): array
    {
        return $this->imports_ ?? [];
    }

    public function addClassPathToImportsPropertyAfter(\Closure $callback)
    {
        return function (string $value) use ($callback) {
            $result = $callback($value);
            $name = $result instanceof ParseClassPathResult ? $result->getComponentName() : $result;
            $classPath = $result instanceof ParseClassPathResult ? $result->getClassPath() : $value;
            $this->imports_ = $this->imports_ ?? [];
            if (!in_array($value, $this->imports_)) {
                $this->imports_[] = $classPath;
            }
            return $name;
        };
    }

    public function getClassFromClassPath(string $classPath)
    {
        $classPathComponents = array_reverse(drewlabs_core_strings_to_array($classPath, "\\"));
        $name = $classPathComponents[0];
        $globalImports_ = $this->getGlobalImports() ?? [];
        $matches = array_filter($globalImports_, function ($import) use ($name) {
            return is_string($import) && drewlabs_core_strings_ends_with($import, $name);
        });
        if (!empty($matches)) {
            $prefix = count($classPathComponents) > 1 ? $classPathComponents[1] : 'Base';
            $name = drewlabs_core_strings_as_camel_case(sprintf("%s%s", $prefix, $name));
            $classPath = sprintf("%s as %s", $classPath, $name);
        }
        return new ParseClassPathResult($name, $classPath);
    }

    public function setGlobalImports(array $values)
    {
        $this->globalImports_ = $values;
        return $this;
    }

    public function getGlobalImports()
    {
        return $this->globalImports_;
    }
}
