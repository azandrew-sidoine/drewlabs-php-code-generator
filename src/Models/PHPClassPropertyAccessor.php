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
    private $propertyName;

    /** @var string|null */
    private $type;

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
        ?string $type = null,
        string $indentation = "\t"
    ) {
        $this->propertyName = $propertyName;
        $this->type = $type;
        $this->indentation = $indentation;
        $this->name = sprintf("get%s", ucfirst($this->propertyName));
    }

    /**
     * Returns the accessor method name
     * 
     * @return string 
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        $components = [];
        $components[] = "/**";
        $components[] = " *";
        $components[] = sprintf(" * Get %s property value", $this->propertyName);
        $components[] = " *";
        $components[] = sprintf(" * @return %s", $this->type ?? 'mixed');
        $components[] = " */";
        $components[] = sprintf("public function %s()%s", $this->name, $this->type ? sprintf(": %s ", $this->type) : '');
        $components[] = "{";
        $components[] = sprintf("    #code...");
        $components[] = sprintf("    return \$this->%s;", $this->propertyName);
        $components[] = "}";

        $components = array_map(function ($c) {
            return $this->indentation . $c;
        }, $components);

        return implode(PHP_EOL, $components);
    }
}
