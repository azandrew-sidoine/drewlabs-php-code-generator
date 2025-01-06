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

class PHPClassPropertyAccessor
{
    /** @var string */
    private $name;

    /** @var string|null */
    private $type;

    /** @var string*/
    private $indentation = "\t";

    /**
     * Class instance initializer
     * 
     * @param string $name
     * @param null|string $type
     * @param string $indentation 
     */
    public function __construct(
        string $name,
        string $type = null,
        string $indentation = "\t"
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->indentation = $indentation;
    }

    public function __toString(): string
    {
        $components = [];
        $components[] = "/**";
        $components[] = " *";
        $components[] = sprintf(" * Reads %s property value", $this->name);
        $components[] = " *";
        $components[] = sprintf(" * @return %s", $this->type ?? 'mixed');
        $components[] = " */";
        $components[] = sprintf("public function get%s(%s\$value)", ucfirst($this->name), $this->type ? sprintf("%s ", $this->type) : '');
        $components[] = "{";
        $components[] = sprintf("    #code...");
        $components[] = sprintf("    return\$this->%s;", $this->name);
        $components[] = "}";

        $components = array_map(function ($c) {
            return $this->indentation . $c;
        }, $components);

        return implode(PHP_EOL, $components);
    }
}
