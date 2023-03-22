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
use Drewlabs\CodeGenerator\Helpers\Arr;
use Drewlabs\CodeGenerator\Helpers\Str;
use Drewlabs\CodeGenerator\Models\Traits\Type;
use Drewlabs\CodeGenerator\Types\PHPTypes;

class PHPFunctionParameter implements FunctionParameterInterface
{
    use Type;

    /**
     * Parameter default value.
     *
     * @var mixed
     */
    private $default_;

    /**
     * Parameter is optional or not.
     *
     * @var bool
     */
    private $isOptional_;

    /**
     * @var bool
     */
    private $isVariadic_ = false;

    /**
     * @var bool
     */
    private $isReference_ = false;

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
        $this->setName($name);
        // Get the type from the parameter value
        if (null === $type) {
            $value = $default;
            $isPHPClassDef = (is_string($value) && (Str::contains($value, '\\') || Str::endsWith($value, 'new') || Str::endsWith($value, '::class')));
            if (is_numeric($value) || $isPHPClassDef) {
                $type = null === $this->type_ ? (is_numeric($value) ? sprintf('%s|%s', PHPTypes::INT, PHPTypes::FLOAT) : sprintf('%s', PHPTypes::OBJECT)) : $this->type_;
            } elseif (is_string($value) && !$isPHPClassDef) {
                $type = null === $this->type_ ? sprintf('%s', PHPTypes::STRING) : $this->type_;
            } elseif (is_array($value)) {
                $type = null === $this->type_ ? sprintf('%s', PHPTypes::LIST) : $this->type_;
            }
        }
        $this->setType($type);
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

    public function asVariadic()
    {
        $this->isVariadic_ = true;

        return $this;
    }

    public function isVariadic()
    {
        return $this->isVariadic_ ?? false;
    }

    public function asReference()
    {
        $this->isReference_ = true;

        return $this;
    }

    public function isReference()
    {
        return $this->isReference_ ?? false;
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

    public function equals(FunctionParameterInterface $value)
    {
        return $this->name() === $value->name();
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
        if (is_string($value) && empty($value)) {
            return '';
        }
        if (\is_bool($value)) {
            return $value === false ? "false" : "true";
        }
        $isPHPClassDef = (is_string($value) && (Str::contains($value, '\\') || Str::endsWith($value, 'new') || Str::endsWith($value, '::class')));
        if (is_numeric($value) || $isPHPClassDef) {
            return "$value";
        } elseif (is_string($value) && !$isPHPClassDef) {
            return "\"$value\"";
        } elseif (is_array($value)) {
            $start = '[';
            foreach ($value as $key => $v) {
                if ($key === Arr::keyLast($value)) {
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
