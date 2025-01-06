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

use Drewlabs\CodeGenerator\Contracts\ValueContainer;
use Drewlabs\CodeGenerator\Exceptions\PHPVariableException;
use Drewlabs\CodeGenerator\Helpers\Str;
use Drewlabs\CodeGenerator\Models\Traits\BelongsToNamespace;
use Drewlabs\CodeGenerator\Models\Traits\HasImportDeclarations;
use Drewlabs\CodeGenerator\Models\Traits\HasIndentation;
use Drewlabs\CodeGenerator\Models\Traits\Type;
use Drewlabs\CodeGenerator\Models\Traits\ValueContainer as TraitsValueContainer;
use Drewlabs\CodeGenerator\Types\PHPTypesModifiers;

class PHPVariable implements ValueContainer
{
    use BelongsToNamespace;
    use HasImportDeclarations;
    use HasIndentation;
    use TraitsValueContainer;
    use Type;

    /**
     * Indicated that the variable definition is an R-Value definition.
     *
     * @var bool
     */
    private $isRValue_;

    /**
     * Class instances initializer.
     *
     * @param string|array $default
     */
    public function __construct(
        string $name,
        ?string $type = null,
        $default = null,
        $descriptors = ''
    ) {
        $this->name = $name;
        if (null !== $type) {
            $this->setType($type);
        }
        if (null !== $descriptors) {
            $this->addComment($descriptors);
        }
        $this->value($default ?? '');
    }

    public function __toString(): string
    {
        $this->prepare()
            ->setComments()
            ->value($this->value());

        $value = $this->parsePropertyValue();
        $name = $this->getName();

        if ($this->isRValue_ && ((null === $value) || empty($value))) {
            throw new PHPVariableException($name, 'R-value object must have a default value');
        }
        // Initialize parts and definition values
        $parts = [];
        $definition = '';
        if (!$this->isRValue_) {
            // Generate comments
            $parts[] = $this->getIndentation() ? $this->comment->setIndentation($this->getIndentation())->__toString() : $this->comment->__toString();
            $definition = $this->isConstant ? sprintf('%s %s', PHPTypesModifiers::CONSTANT, Str::upper($name)) : sprintf('$%s', $name);
        }
        if (Str::contains($value, "'[") && Str::contains($value, "]'")) {
            $value = str_replace(" ]'", ']', str_replace("'[", '[', $value));
        }
        $definition .= $value && \is_string($value) && !empty($value) ?  str_replace('"null"', 'null', str_replace(["''"], "'", str_replace(['""'], '"', $this->isRValue_ ? "$value;" : " = $value;"))) : ';';
        $parts[] = $definition;
        if ($this->getIndentation()) {
            $parts = array_map(function ($part) {
                return $this->getIndentation() . $part;
            }, $parts);
        }

        return implode(\PHP_EOL, $parts);
    }

    /**
     * Defines the variable as an r-value object.
     *
     * Note: R-Value variable does not have or type definition. Their type in inducted by the compiler.
     * They only have the value definition part
     *
     * @return self
     */
    public function asRValue()
    {
        $this->isRValue_ = true;

        return $this;
    }
}
