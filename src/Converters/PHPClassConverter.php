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

use Drewlabs\CodeGenerator\Contracts\Blueprint;
use Drewlabs\CodeGenerator\Contracts\Converters\Stringifier;
use Drewlabs\CodeGenerator\Contracts\ValueContainer;
use Drewlabs\CodeGenerator\Models\PHPClassMethod;
use Drewlabs\CodeGenerator\Models\PHPClassProperty;
use Drewlabs\CodeGenerator\Models\PHPNamespace;

class PHPClassConverter implements Stringifier
{
    /**
     * {@inheritDoc}
     *
     * @param Blueprint $component
     *
     * @throws \InvalidArgumentException
     */
    public function stringify($component): string
    {
        if (!($component instanceof Blueprint)) {
            throw new \InvalidArgumentException(__CLASS__.' only support stringifying class/blueprint component');
        }
        $namespace = $component->getNamespace();
        if (null !== $namespace) {
            return $this->buildNamespaceClass($namespace, $component);
        }

        return $this->blueprintToString($component);
    }

    /**
     * Returns the class a PHP string that can be write to a file.
     */
    protected function blueprintToString(Blueprint $clazz): string
    {
        // Setting import is done in the blueprint definition
        $parts = [];
        $modifier = $clazz->isFinal() ? 'final ' : ($clazz->isAbstract() ? 'abstract ' : '');
        $declaration = sprintf('%sclass %s', $modifier, $clazz->getName());
        $baseClazz = $clazz->getBaseClass();
        if ((null !== $baseClazz)) {
            $declaration .= sprintf(' extends %s', $baseClazz);
        }
        // Get class implementations
        $implementations = $clazz->getImplementations();
        if ((null !== $implementations) && \is_array($implementations) && !empty($implementations)) {
            $declaration .= sprintf(' implements %s', implode(', ', $implementations));
        }
        $parts[] = $declaration;
        $parts[] = '{';
        // Add Traits
        $traits = $clazz->getTraits();
        if (null !== $traits && \is_array($traits) && !empty($traits)) {
            $parts[] = '';
            foreach ($traits as $value) {
                // code...
                $parts[] = "\tuse $value;";
            }
        }
        $imports = $clazz->getImports();

        // Add properties
        $properties = $clazz->getProperties();
        if ((null !== $properties) && \is_array($properties) && !empty($properties)) {
            foreach ($properties as $value) {
                $parts[] = '';
                if (($value instanceof PHPClassProperty) || method_exists($value, 'addToNamespace')) {
                    /**
                     * @var ValueContainer
                     */
                    $value = $value->{'addToNamespace'}($clazz->getNamespace());
                }
                $parts[] = $value->setIndentation("\t")->__toString();
                $imports = array_merge($imports, $value->getImports() ?? []);
            }
        }
        // Add class methods
        $methods = $clazz->getMethods();
        if ((null !== $methods) && \is_array($methods) && !empty($methods)) {
            foreach ($methods as $value) {
                $parts[] = '';
                if (($value instanceof PHPClassMethod) || method_exists($value, 'addToNamespace')) {
                    /**
                     * @var ValueContainer
                     */
                    $value = $value->{'addToNamespace'}($clazz->getNamespace());
                }
                $parts[] = $value->setGlobalImports($imports)->setIndentation("\t")->__toString();
                $imports = array_merge($imports, $value->getImports() ?? []);
            }
        }
        $clazz->setGlobalImports($imports);
        $parts[] = '';
        $parts[] = '}';

        return implode(\PHP_EOL, $parts);
    }

    protected function buildNamespaceClass(string $namespace, Blueprint $clazz)
    {
        $classString = $this->blueprintToString($clazz);
        $parts[] = (new PHPNamespace($namespace))
            ->addClass($clazz)
            ->addImports($clazz->getGlobalImports() ?? [])->__toString();
        $parts[] = $classString;

        return implode(\PHP_EOL, $parts);
    }
}
