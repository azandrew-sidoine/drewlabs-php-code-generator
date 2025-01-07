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

namespace Drewlabs\CodeGenerator\Converters;

use Drewlabs\CodeGenerator\Contracts\Converters\Stringifier;
use Drewlabs\CodeGenerator\Helpers\Types;

class PHPValueStringifier implements Stringifier
{
    /** @var null|string */
    private $indentation;

    /**
     * PHP value stringifier instance initializer
     * 
     * @param null|string $indentation
     */
    public function __construct(?string $indentation = null)
    {
        $this->indentation = $indentation;
    }

    /**
     * Class factory constructor
     * 
     * @param string|null $indentation
     * @return static
     */
    public static function new(?string $indentation = null)
    {
        return new static($indentation);
    }

    public function stringify($value): string
    {
        if (is_string($value) && empty($value)) {
            return '';
        }

        $isClassDeclaration = Types::isClassDeclaration($value);
        if (\is_bool($value)) {
            return $value === false ? "false" : "true";
        }
        
        if (is_numeric($value) || $isClassDeclaration) {
            return "$value";
        }
        
        if (is_string($value) && !$isClassDeclaration) {
            return $this->compileScalar($value);
        }
        
        if (is_array($value)) {
            if (empty($value)) {
                $start = '[]';
            } else {
                $start = '[' . \PHP_EOL;
                foreach ($value as $key => $v) {
                    $compile = function ($item) {
                        return is_array($item) ? $this->compileArray($item) : $this->compileScalar($item);
                    };
                    $format = function ($key, $item) {
                        return is_numeric($key) ? "\t'%s'," : (is_numeric($item) || is_array($item) ? "\t'%s' => %s," : "\t'%s' => '%s',");
                    };
                    $def = is_numeric($key) ? sprintf($format($key, $v), $compile($v))  . \PHP_EOL : sprintf($format($key, $v), $key, $compile($v)) . \PHP_EOL;
                    $start .= $this->indentation ? $this->indentation . $def : $def;
                }
                $start .= $this->indentation ? $this->indentation . ']' : ']';
            }

            return $start;
        }

        return null === $value ? 'null' : '';
    }



    /**
     * Compile a scalar expression
     * 
     * @param string $value 
     * @return string 
     */
    private function compileScalar(string $value)
    {
        return (is_string($value) && substr($value, 0, strlen('expr:')) === 'expr:' || is_numeric($value) ? str_replace('expr:', '', (string)$value) : "'$value'");
    }

    /**
     * Compile array into a string value
     * 
     * @param array $variable 
     * @param string $indentation 
     * @return string 
     */
    private function compileArray(array $variable, string $indentation = "\t")
    {
        $output = ['['];
        $prettify = false;
        foreach ($variable as $key => $value) {
            if (is_array($value)) {
                $prettify = true;
                $output[] = $indentation . "\t" . (is_numeric($key) ? '' : "'$key' => ") . $this->compileArray($value, "\t" . $indentation) . ', ';
                continue;
            }
            $output[] = $indentation . "\t" . (is_numeric($key) ? '' : "'$key' => ") .  $this->compileScalar($value) . ', ';
        }
        $output[count($output) - 1] = rtrim($output[count($output) - 1], ', ');
        $output[] = $prettify ? \PHP_EOL . $indentation . ']' : ']';
        if ($prettify) {
            for ($i = 1; $i < count($output) - 1; $i++) {
                $output[$i] = \PHP_EOL . $output[$i];
            }
        } else {
            for ($i = 1; $i < count($output) - 1; $i++) {
                $output[$i] = ltrim($output[$i], $indentation);
            }
        }
        return str_replace("''", "'", implode($output));
    }
}
