<?php

namespace Drewlabs\CodeGenerator\Models;

use Drewlabs\CodeGenerator\Contracts\ValueContainer;
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
     * Class instances initializer.
     *
     * @param string       $modifier
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
        // Generate comments
        if ($this->getIndentation()) {
            $parts[] = $this->comment_->setIndentation($this->getIndentation())->__toString();
        } else {
            $parts[] = $this->comment_->__toString();
        }

        $definition = $this->isConstant_ ? drewlabs_core_strings_to_upper_case(sprintf('%s %s', PHPTypesModifiers::CONSTANT, $this->getName())) : sprintf("$%s", $this->getName());
        // TODO : Review this part after all classes tested successfully
        $value = $this->parsePropertyValue();
        if (drewlabs_core_strings_contains($value, '"[') && drewlabs_core_strings_contains($value, ']"')) {
            $value = drewlabs_core_strings_replace(' ]"', ']', drewlabs_core_strings_replace('"[', '[', $value));
        }
        if ($value && \is_string($value) && !empty($value)) {
            $definition .= drewlabs_core_strings_replace('"null"', 'null', drewlabs_core_strings_replace(['""'], '"', " = $value;"));
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
