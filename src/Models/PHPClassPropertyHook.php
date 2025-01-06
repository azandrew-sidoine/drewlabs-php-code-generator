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

use Drewlabs\CodeGenerator\Types\PHPTypesModifiers;

class PHPClassPropertyHook
{
    /** @var string */
    private $name;

    /** @var string */
    private $modifier;

    /** @var string|null */
    private $type;

    /** @var bool */
    private $mutable;

    /** @var string*/
    private $indentation = "\t";

    /**
     * Class instance initializer
     * 
     * @param string $name
     * @param ?string $modifier
     * @param null|string $type
     * @param bool $mutable
     * @param string $indentation 
     */
    public function __construct(
        string $name,
        string $type = null,
        ?string $modifier = PHPTypesModifiers::PUBLIC,
        bool $mutable = true,
        string $indentation = "\t"
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->modifier = $modifier ?? PHPTypesModifiers::PUBLIC;
        $this->mutable = $mutable;
        $this->indentation = $indentation;
    }


    public function __toString(): string
    {
        $components = [];
        $params = [$this->modifier ?? PHPTypesModifiers::PUBLIC, $this->type ? sprintf(" %s", $this->type) : '', $this->name];
        $components[] = ($this->mutable ? sprintf("%s%s %s", ...$params) : sprintf("%s private(set)%s %s", ...$params));
        $components[] = "{";

        if ($this->mutable) {
            $components[] = sprintf("  set (%s\$value) {", $this->type ? sprintf("%s ", $this->type) : '');
            $components[] = sprintf("    #code...");
            $components[] = sprintf("    \$this->%s = \$value;", $this->name);
            $components[] = "  }";
        }

        $components[] = "";
        $components[] = "  get () {";
        $components[] = sprintf("    #code...");
        $components[] = sprintf("    return\$this->%s;", $this->name);
        $components[] = "  }";
        $components[] = "}";

        $components = array_map(function($c) {
            return $this->indentation . $c;
        }, $components);

        return implode(PHP_EOL, $components);
    }
}
