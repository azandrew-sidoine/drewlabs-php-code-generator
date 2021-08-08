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

use Drewlabs\CodeGenerator\Contracts\FunctionParameterInterface;
use Drewlabs\CodeGenerator\Types\PHPTypes;

class PHPFunctionParameter implements FunctionParameterInterface
{
    /**
     * Parameter type.
     *
     * @var string
     */
    private $type_;

    /**
     * Parameter name.
     *
     * @var string
     */
    private $name_;

    /**
     * Parameter default value.
     *
     * @var string
     */
    private $default_;

    /**
     * Parameter is optional or not.
     *
     * @var string
     */
    private $isOptional_;

    /**
     * Instance initializer.
     *
     * @param string|string[]|null $default
     */
    public function __construct(
        string $name,
        ?string $type = null,
        $default = null
    ) {
        $this->name_ = $name;
        // Get the type from the parameter value
        if (null === $type) {
            $value = $default;
            $isPHPClassDef = (drewlabs_core_strings_is_str($value) && (drewlabs_core_strings_contains($value, '\\') || drewlabs_core_strings_starts_with($value, 'new') || drewlabs_core_strings_ends_with($value, '::class')));
            if (is_numeric($value) || $isPHPClassDef) {
                $this->type_ = null === $this->type_ ? (is_numeric($value) ? sprintf('%s|%s', PHPTypes::INT, PHPTypes::FLOAT) : sprintf('%s', PHPTypes::OBJECT)) : $this->type_;
            } elseif (drewlabs_core_strings_is_str($value) && !$isPHPClassDef) {
                $this->type_ = null === $this->type_ ? sprintf('%s', PHPTypes::STRING) : $this->type_;
            } elseif (drewlabs_core_array_is_arrayable($value)) {
                $this->type_ = null === $this->type_ ? sprintf('%s', PHPTypes::LIST) : $this->type_;
            }
        } else {
            $this->type_ = $type;
        }
        $this->default_ = $default;
        $this->isOptional_ = null !== $default ? true : false;
    }

    /**
     * Indicates that the parameter is optional.
     *
     * @return bool
     */
    public function isOptional()
    {
        return $this->isOptional_;
    }

    /**
     * Creates an optional method / function parameter.
     *
     * @return self
     */
    public function asOptional()
    {
        $this->isOptional_ = true;

        return $this;
    }

    /**
     * Returns the parameter name.
     *
     * @return string
     */
    public function name()
    {
        return $this->name_;
    }

    /**
     * Returns the parameter type name.
     *
     * @return string
     */
    public function type()
    {
        return $this->type_;
    }

    public function equals(FunctionParameterInterface $value)
    {
        return $this->name_ === $value->name();
    }

    /**
     * Returns the parameter default value.
     *
     * @return string
     */
    public function defaultValue()
    {
        $value = $this->default_;
        if (null === $value) {
            return $this->isOptional() ? 'null' : null;
        }
        // Return the object is an empry string or array is passed in
        if (empty($value)) {
            return '';
        }
        $isPHPClassDef = (drewlabs_core_strings_is_str($value) && (drewlabs_core_strings_contains($value, '\\') || drewlabs_core_strings_starts_with($value, 'new') || drewlabs_core_strings_ends_with($value, '::class')));
        if (is_numeric($value) || $isPHPClassDef) {
            return "$value";
        } elseif (drewlabs_core_strings_is_str($value) && !$isPHPClassDef) {
            return "\"$value\"";
        } elseif (drewlabs_core_array_is_arrayable($value)) {
            $start = '[';
            foreach ($value as $key => $v) {
                if ($key === drewlabs_core_array_key_last($value)) {
                    $start .= ' '.(is_numeric($key) ? sprintf('"%s"', $v) : (is_numeric($v) ? sprintf('"%s" => %s', $key, $v) : sprintf('"%s" => "%s"', $key, $v)));
                    continue;
                }
                $start .= ' '.(is_numeric($key) ? sprintf(' "%s",', $v) : (is_numeric($v) ? sprintf(' "%s" => %s,', $key, $v) : sprintf(' "%s" => "%s",', $key, $v)));
            }
            $start .= ' ]';

            return $start;
        }

        return $this->default_;
    }
}
