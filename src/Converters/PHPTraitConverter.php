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
use Drewlabs\CodeGenerator\Contracts\TraitableStruct;
use Drewlabs\CodeGenerator\Models\PHPNamespace;

class PHPTraitConverter implements Stringifier
{
    public function typeToString(TraitableStruct $component): string
    {
        $parts = [];
        $parts[] = sprintf('trait %s', $component->getName());
        $parts[] = '{';
        // Add Traits
        $traits = $component->getTraits();
        if (null !== $traits && \is_array($traits) && !empty($traits)) {
            $parts[] = '';
            foreach ($traits as $value) {
                // code...
                $parts[] = "\tuse $value;";
            }
        }
        $imports = $component->getImports();
        // Add properties
        $properties = $component->getProperties();
        if ((null !== $properties) && \is_array($properties) && !empty($properties)) {
            foreach ($properties as $value) {
                $parts[] = '';
                $parts[] = $value->setIndentation("\t")->__toString();
                $imports = array_merge($imports, $value->getImports() ?? []);
            }
        }
        // Add method defintions
        $methods = $component->getMethods();
        if ((null !== $methods) && \is_array($methods) && !empty($methods)) {
            foreach ($methods as $value) {
                $parts[] = '';
                $parts[] = $value->setGlobalImports($imports)->setIndentation("\t")->__toString();
                $imports = array_merge($imports, $value->getImports() ?? []);
            }
        }
        $component->setGlobalImports($imports);
        $parts[] = '';
        $parts[] = '}';

        return implode(\PHP_EOL, $parts);
    }

    /**
     * {@inheritDoc}
     *
     * @param TraitableStruct $component
     *
     * @throws \InvalidArgumentException
     */
    public function stringify($component): string
    {
        if (!($component instanceof TraitableStruct)) {
            throw new \InvalidArgumentException(__CLASS__.' only support converting '.TraitableStruct::class.' to string');
        }
        $namespace = $component->getNamespace();
        if (null !== $namespace) {
            return $this->typeWithNamespaceToString($namespace, $component);
        }

        return $this->typeToString($component);
    }

    protected function typeWithNamespaceToString(string $namespace, TraitableStruct $component)
    {
        $traitString = $this->typeToString($component);
        $parts[] = (new PHPNamespace($namespace))
        ->addTrait($component)
        ->addImports($component->getGlobalImports())->__toString();
        $parts[] = $traitString;

        return implode(\PHP_EOL, $parts);
    }
}
