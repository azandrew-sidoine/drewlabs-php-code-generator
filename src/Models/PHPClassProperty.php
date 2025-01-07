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

use Drewlabs\CodeGenerator\Contracts\ClassMemberInterface;
use Drewlabs\CodeGenerator\Contracts\ValueContainer;
use Drewlabs\CodeGenerator\Helpers\Str;
use Drewlabs\CodeGenerator\Models\Traits\BelongsToNamespace;
use Drewlabs\CodeGenerator\Models\Traits\HasImportDeclarations;
use Drewlabs\CodeGenerator\Models\Traits\HasIndentation;
use Drewlabs\CodeGenerator\Models\Traits\HasPHP8Attributes;
use Drewlabs\CodeGenerator\Models\Traits\OOPStructComponentMembers;
use Drewlabs\CodeGenerator\Models\Traits\Type;
use Drewlabs\CodeGenerator\Models\Traits\ValueContainer as TraitsValueContainer;
use Drewlabs\CodeGenerator\Types\PHPTypesModifiers;
use Drewlabs\CodeGenerator\Contracts\HasPHP8Attributes as AbstractHasPHP8Attributes;
use Drewlabs\CodeGenerator\Contracts\PropertyInterface;

class PHPClassProperty implements ValueContainer, ClassMemberInterface, AbstractHasPHP8Attributes, PropertyInterface
{
    use Type;
    use HasIndentation;
    use HasPHP8Attributes;
    use BelongsToNamespace;
    use TraitsValueContainer;
    use HasImportDeclarations;
    use OOPStructComponentMembers;

    /** @var bool */
    private $hasMutator = false;

    /** @var bool */
    private $hasAccessor = false;

    /** @var bool */
    private $immutable = false;

    /** @var bool */
    private $hasHooks = false;

    /**
     * Class instances initializer.
     * 
     * @param string $name 
     * @param null|string $type 
     * @param null|string $modifier
     * @param mixed $default 
     * @param string $descriptors 
     */
    public function __construct(
        string $name,
        ?string $type = null,
        ?string $modifier = 'public',
        $default = null,
        $descriptors = ''
    ) {
        $this->setName($name);
        if (null !== $type) {
            $this->setType($type);
        }
        if (null !== $descriptors) {
            $this->addComment($descriptors);
        }
        if (null !== $modifier) {
            $this->setModifier($modifier);
        }
        $this->value($default);
    }

    public function setHasAccessor(bool $value = true)
    {
        $this->hasAccessor = $value;
        return $this;
    }

    public function setHasMutator(bool $value = true)
    {
        $this->hasMutator = $value;
        return $this;
    }

    public function setHasHooks(bool $value = true)
    {
        $this->hasHooks = $value;
        return $this;
    }

    public function setImmutable(bool $value = true)
    {
        $this->immutable = $value;
        return $this;
    }

    public function hasMutator(): bool
    {
        return $this->hasMutator;
    }

    public function hasAccessor(): bool
    {
        return $this->hasAccessor;
    }

    public function hasHooks(): bool
    {
        return $this->hasHooks;
    }

    public function isImmutable(): bool
    {
        return $this->hasMutator && $this->immutable;
    }

    public function __toString(): string
    {
        $this->prepare()->setComments()->value($this->value());
        $value = $this->parsePropertyValue();
        $name = $this->getName();
        // Generate comments
        if ($this->getIndentation()) {
            $parts[] = $this->comment->setIndentation($this->getIndentation())->__toString();
        } else {
            $parts[] = $this->comment->__toString();
        }

        // Add PHP8 attributes to method/function definition
        foreach ($this->getAttributes() as $attribute) {
            $parts[] = sprintf("%s", PHP8Attribute::new($attribute)->__toString());
        }

        // Generate defintion / declarations
        $modifier = (null !== $this->accessModifier()) && \in_array(
            $this->accessModifier(),
            ['private', 'protected', 'public'],
            true
        ) ? $this->accessModifier() : PHPTypesModifiers::PUBLIC;

        $definition = $this->isConstant ? sprintf('%s %s %s', $modifier, PHPTypesModifiers::CONSTANT, Str::upper($name)) : sprintf('%s $%s', $modifier, $name);
        if (Str::contains($value, "'[") && Str::contains($value, "]'")) {
            $value = str_replace(" ]'", ']', str_replace("'[", '[', $value));
        }

        if ($value && \is_string($value) && !empty($value) && !(trim($value) === 'null' || trim($value) === 'NULL')) {
            $definition .= str_replace('"null"', 'null', str_replace(["''"], "'", str_replace(['""'], '"', " = $value;")));
        } else {
            $definition .= ';';
        }

        $parts[] = $definition;
        if ($this->getIndentation()) {
            $parts = array_map(function ($part) {
                return $this->getIndentation() . $part;
            }, $parts);
        }

        return implode(\PHP_EOL, $parts);
    }
}
