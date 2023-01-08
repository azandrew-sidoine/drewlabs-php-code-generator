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
        $this->name_ = $name;
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
            $parts[] = $this->getIndentation() ? $this->comment_->setIndentation($this->getIndentation())->__toString() : $this->comment_->__toString();
            $definition = $this->isConstant_ ? sprintf('%s %s', PHPTypesModifiers::CONSTANT, drewlabs_core_strings_to_upper_case($name)) : sprintf('$%s', $name);
        }
        if (drewlabs_core_strings_contains($value, "'[") && drewlabs_core_strings_contains($value, "]'")) {
            $value = drewlabs_core_strings_replace(" ]'", ']', drewlabs_core_strings_replace("'[", '[', $value));
        }
        $definition .= $value && \is_string($value) && !empty($value) ?  drewlabs_core_strings_replace('"null"', 'null', drewlabs_core_strings_replace(['""'], '"', $this->isRValue_ ? "$value;" : " = $value;")) : ';';
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
