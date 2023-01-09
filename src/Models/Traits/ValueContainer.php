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

use Drewlabs\CodeGenerator\Contracts\ValueContainer as ContractsValueContainer;
use function Drewlabs\CodeGenerator\Proxy\CommentFactory;

use Drewlabs\CodeGenerator\Types\PHPTypes;

trait ValueContainer
{
    /**
     * List of imports to append to the file/class imports.
     *
     * @var string[]
     */
    private $imports_;

    /**
     * PHP Stringeable component.
     *
     * @var mixed
     */
    private $comment_;

    /**
     * The default value to set the property to.
     *
     * @var string|array|null
     */
    private $value_;

    /**
     * Indicates that the property is a constant property.
     *
     * @var bool
     */
    private $isConstant_ = false;

    public function value($value = null)
    {
        // Act like a property getter when nothing is passed
        if (null === $value) {
            return $this->value_;
        }
        $this->value_ = $value;
        return $this;
    }

    public function asConstant()
    {
        $this->isConstant_ = true;

        return $this;
    }

    public function equals(ContractsValueContainer $value)
    {
        return $this->name_ === $value->getName();
    }

    protected function prepare()
    {
        $type = $this->type();
        if ((null !== $type) && drewlabs_core_strings_contains($type, '\\')) {
            $this->setType($this->addClassPathToImportsPropertyAfter(function ($classPath) {
                return $this->getClassFromClassPath($classPath);
            })($type));
        }

        return $this;
    }

    protected function setComments()
    {
        $type = $this->type();
        /**
         * @var string[]
         */
        $descriptors = $this->comments();
        if (!empty($descriptors)) {
            // Add a line separator between the descriptors and other definitions
            $descriptors[] = '';
        }
        $this->comment_ = (CommentFactory(true))->make(
            !empty($descriptors) ?
                ($type ? array_merge(
                    $descriptors ?? [],
                    [
                        "@var $type",
                    ]
                ) : array_merge(
                    $descriptors ?? [],
                    [
                        '@var mixed',
                    ]
                )) : ($type ? [
                    "@var $type",
                ] : [
                    '@var mixed',
                ])
        );

        return $this;
    }

    private function parsePropertyValue()
    {
        $value = $this->value_;
        $type = $this->type();
        // Return the object is an empry string or array is passed in
        if (drewlabs_core_strings_is_str($value) && empty($value)) {
            return '';
        }
        $isPHPClassDef = (drewlabs_core_strings_is_str($value) &&
            (drewlabs_core_strings_contains($value, '\\') ||
                drewlabs_core_strings_starts_with($value, 'new') ||
                drewlabs_core_strings_ends_with($value, '::class')));
        if (\is_bool($value)) {
            $this->setType(null === $type ? sprintf('%s', PHPTypes::BOOLEAN) : $type);
            return $value === false ? "false" : "true";
        } elseif (is_numeric($value) || $isPHPClassDef) {
            $this->setType(null === $type ? (is_numeric($value) ? sprintf('%s|%s', PHPTypes::INT, PHPTypes::FLOAT) : sprintf('%s', PHPTypes::OBJECT)) : $type);
            return "$value";
        } elseif (drewlabs_core_strings_is_str($value) && !$isPHPClassDef) {
            $this->setType(null === $type ? sprintf('%s', PHPTypes::STRING) : $type);
            return $this->compileScalar($value);
        } elseif (drewlabs_core_array_is_arrayable($value)) {
            $this->setType(null === $type ? sprintf('%s', PHPTypes::LIST) : $type);
            $indentation = $this->getIndentation();
            if (empty($value)) {
                $start = '[]';
            } else {
                $start = '[' . \PHP_EOL;
                foreach ($value as $key => $v) {
                    $evaluateValue = function ($item) {
                        return is_array($item) ?
                            $this->compileArray($item) : $this->compileScalar($item);
                    };
                    $formatFunc = function ($key, $item) {
                        return is_numeric($key) ? "\t'%s'," : (is_numeric($item) || is_array($item) ? "\t'%s' => %s," : "\t'%s' => '%s',");
                    };
                    $def = is_numeric($key) ?
                        sprintf($formatFunc($key, $v), $evaluateValue($v))  . \PHP_EOL :
                        sprintf($formatFunc($key, $v), $key, $evaluateValue($v)) . \PHP_EOL;
                    $start .= $indentation ? $indentation . $def : $def;
                }
                $start .= $indentation ? $indentation . ']' : ']';
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
