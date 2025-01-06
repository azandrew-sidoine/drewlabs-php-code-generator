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

namespace Drewlabs\CodeGenerator\Models;

use Drewlabs\CodeGenerator\Contracts\Stringable;
use Drewlabs\CodeGenerator\Helpers\Str;

class PHPNamespace implements Stringable
{
    /** @var string */
    private $ns;

    /**
     * Class definition models.
     *
     * @var PHPClass[]
     */
    private $classes = [];

    /**
     * Trait definition models.
     *
     * @var PHPTrait[]
     */
    private $traits = [];

    /**
     * Interfaces definition models.
     *
     * @var PHPInterface[]
     */
    private $interfaces = [];

    /** @var string[] */
    private $imports = [];

    /**
     * Undocumented function.
     */
    public function __construct(string $ns)
    {
        $this->ns = $ns;
    }

    public function __toString(): string
    {
        $parts = $this->ns ? ["namespace $this->ns;"] : [];
        $imports = array_unique(array_map(static function ($import) {
            return \is_string($import) ? ltrim($import, '\\') : $import;
        }, $this->imports));
        $parts[] = '';
        $functions_imports = [];
        $class_imports = [];
        foreach ($imports as $value) {
            if (Str::startsWith($value, 'function ')) {
                $functions_imports[] = "use $value;";
            } else {
                $class_imports[] = "use $value;";
            }
        }
        $parts = array_merge($parts, $class_imports);
        if (\count($functions_imports) > 0) {
            $parts[] = \PHP_EOL.'// Function import statements';
            $parts = array_merge($parts, $functions_imports);
        }
        $parts[] = '';
        // Add content here
        return implode(\PHP_EOL, $parts);
    }

    /**
     * Add a class to the namespace.
     *
     * @return self
     */
    public function addClass(PHPClass $blueprint)
    {
        $this->classes[] = $blueprint;

        return $this;
    }

    /**
     * Add a trait to the namespace.
     *
     * @return self
     */
    public function addTrait(PHPTrait $value)
    {
        $this->traits[] = $value;

        return $this;
    }

    /**
     * Add an interface to the namespace.
     *
     * @return self
     */
    public function addInterface(PHPInterface $value)
    {
        $this->interfaces[] = $value;

        return $this;
    }

    /**
     * Add list of imports to the namespace definitions.
     *
     * @param string[] $imports
     *
     * @return self
     */
    public function addImports(array $imports)
    {
        $this->imports = $imports;

        return $this;
    }

    public function buildClasses()
    {
        $classes = [];
        foreach (($this->classes ?? []) as $value) {
            $classes[$value->getName()] = $value->addToNamespace($this->ns)->__toString();
        }

        return $classes;
    }

    public function buildTraits()
    {
        $traits = [];
        foreach (($this->traits ?? []) as $value) {
            $traits[$value->getName()] = $value->addToNamespace($this->ns)->__toString();
        }
        return $traits;
    }
}
