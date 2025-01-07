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

class PHPClassPropertyMutator
{
    /** @var string */
    private $propertyName;

    /** @var string|null */
    private $type;

    /** @var bool */
    private $immutable;

    /** @var string*/
    private $indentation = "\t";

    /** @var string */
    private $name;

    /**
     * Class instance initializer
     * 
     * @param string $propertyName
     * @param null|string $type
     * @param string $indentation 
     */
    public function __construct(
        string $propertyName,
        string $type = null,
        bool $immutable = false,
        string $indentation = "\t"
    ) {
        $this->propertyName = $propertyName;
        $this->type = $type;
        $this->immutable = $immutable ?? false;
        $this->indentation = $indentation;
        $this->name = sprintf("set%s", ucfirst($this->propertyName));
    }

    /**
     * Returns the mutator method name
     * 
     * @return string 
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        $ref = $this->immutable ? 'self' : 'this';
        $components = [];
        $components[] = "/**";
        $components[] = sprintf(" * Mutates %s property value", $this->propertyName);
        $components[] = " *";
        $components[] = sprintf(" * @param %s %s", $this->type ?? 'mixed', $this->propertyName);
        $components[] = " *";
        $components[] = " * @return static";
        $components[] = " */";
        $components[] = sprintf("public function %s(%s\$value)", $this->name, $this->type ? sprintf("%s ", $this->type) : '');
        $components[] = "{";
        $components[] = sprintf("    #code...");
        if ($this->immutable) {
            $components[] = "    \$self = clone \$this;";
        }
        $components[] = sprintf("    \$%s->%s = \$value;", $ref, $this->propertyName);
        $components[] = sprintf("    return \$%s;", $ref);
        $components[] = "}";

        $components = array_map(function ($c) {
            return $this->indentation . $c;
        }, $components);

        return implode(PHP_EOL, $components);
    }
}
