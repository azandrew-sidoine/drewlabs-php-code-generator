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

use Drewlabs\CodeGenerator\CommentFactory;
use Drewlabs\CodeGenerator\Contracts\Blueprint;
use Drewlabs\CodeGenerator\Contracts\Converters\Stringifier;
use Drewlabs\CodeGenerator\Contracts\HasPHP8Attributes;
use Drewlabs\CodeGenerator\Contracts\PropertyInterface;
use Drewlabs\CodeGenerator\Contracts\ValueContainer;
use Drewlabs\CodeGenerator\Models\PHP8Attribute;
use Drewlabs\CodeGenerator\Models\PHPClassMethod;
use Drewlabs\CodeGenerator\Models\PHPClassProperty;
use Drewlabs\CodeGenerator\Models\PHPClassPropertyAccessor;
use Drewlabs\CodeGenerator\Models\PHPClassPropertyHook;
use Drewlabs\CodeGenerator\Models\PHPClassPropertyMutator;
use Drewlabs\CodeGenerator\Models\PHPNamespace;
use Drewlabs\CodeGenerator\Types\PHPTypesModifiers;
use Drewlabs\CodeGenerator\Contracts\CallableInterface;

class PHPClassStringifier implements Stringifier
{
    /** @var bool */
    private $promoteProperties = false;

    /**
     * Class instance initializer
     * 
     * @param bool $promoteProperties 
     * @return void 
     */
    public function __construct(bool $promoteProperties = false)
    {
        $this->promoteProperties = $promoteProperties;
    }

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
            throw new \InvalidArgumentException(__CLASS__ . ' only support stringifying class/blueprint component');
        }
        $namespace = $component->getNamespace();
        if (null !== $namespace) {
            return $this->buildNamespaceClass($namespace, $component);
        }

        return $this->compile($component);
    }

    /**
     * Returns the class a PHP string that can be write to a file.
     */
    protected function compile($blueprint): string
    {
        // Setting import is done in the blueprint definition
        $parts = [];
        if (!empty($blueprint->comments())) {
            $parts[] = (string)((new CommentFactory(true))->make($blueprint->comments()));
        }
        if ($blueprint instanceof HasPHP8Attributes) {
            foreach ($blueprint->getAttributes() as $attribute) {
                $parts[] = PHP8Attribute::new($attribute)->__toString();
            }
        }

        $modifier = $blueprint->isFinal() ? 'final ' : ($blueprint->isAbstract() ? 'abstract ' : '');
        $declaration = sprintf('%sclass %s', $modifier, $blueprint->getName());
        $baseClazz = $blueprint->getBaseClass();
        if (!empty($baseClazz)) {
            $declaration .= sprintf(' extends %s', $baseClazz);
        }
        // Get class implementations
        $implementations = $blueprint->getImplementations();
        if ((null !== $implementations) && \is_array($implementations) && !empty($implementations)) {
            $declaration .= sprintf(' implements %s', implode(', ', $implementations));
        }
        $parts[] = $declaration;
        $parts[] = '{';
        // Add Traits
        $traits = $blueprint->getTraits();
        if (null !== $traits && \is_array($traits) && !empty($traits)) {
            $parts[] = '';
            foreach ($traits as $value) {
                // code...
                $parts[] = "\tuse $value;";
            }
        }
        $imports = $blueprint->getImports();

        /** @var ValueContainer[] */
        $properties = $blueprint->getProperties() ?? [];
        /** @var CallableInterface[] */
        $methods = $blueprint->getMethods() ?? [];
        $methodNames = array_map(function ($method) {
            return $method->getName();
        }, $methods);
        $bfPHP8Hooks = [];

        // Add properties
        if (!$this->promoteProperties) {
            foreach ($properties as $property) {
                $parts[] = '';
                if (($property instanceof PHPClassProperty) || method_exists($property, 'addToNamespace')) {
                    $property = $property->addToNamespace($blueprint->getNamespace());
                }
                $imports = array_merge($imports, $property->getImports() ?? []);
                if ($property instanceof PropertyInterface && ($property->hasMutator() || $property->hasAccessor()) && $property->usesHooks() && (version_compare(\PHP_VERSION, '8.4') >= 0)) {
                    $hook = new PHPClassPropertyHook($property->getName(), $property->getType(), PHPTypesModifiers::PUBLIC, $property->hasMutator(), $property->value(), "\t");
                    $parts[] = $hook->__toString();
                } else if ($property instanceof PropertyInterface && ($property->hasMutator() || $property->hasAccessor())) {
                    $parts[] = $property->setIndentation("\t")->__toString();
                    $accessor = new PHPClassPropertyAccessor($property->getName(), $property->getType(), "\t");
                    // Case an accessor name is not already defined in class method, we add the accessor as method
                    if (!in_array($accessor->getName(), $methodNames)) {
                        $bfPHP8Hooks[] = $accessor->__toString();
                    }

                    if ($property->hasMutator()) {
                        $mutator = new PHPClassPropertyMutator($property->getName(), $property->getType(), $property->isImmutable(), "\t");
                        // Case an mutator name is not already defined in class method, we add the mutator as method
                        if (!in_array($mutator->getName(), $methodNames)) {
                            $bfPHP8Hooks[] = $mutator->__toString();
                        }
                    }
                } else {
                    $parts[] = $property->setIndentation("\t")->__toString();
                }
            }
        }

        // Add Class constructor
        /** @var CallableInterface */
        if ($constructor = $blueprint->getConstructor()) {
            $parts[] = '';
            if (($constructor instanceof PHPClassMethod) || method_exists($constructor, 'addToNamespace')) {
                $constructor = $constructor->{'addToNamespace'}($blueprint->getNamespace());
            }
            $parts[] = $constructor->setGlobalImports($imports)->setIndentation("\t")->__toString();
            $imports = array_merge($imports, $constructor->getImports() ?? []);
            $parts[] = '';
        }

        // Add the hooks source code if any
        foreach ($bfPHP8Hooks as $hook) {
            $parts[] = $hook;
            $parts[] = '';
        }

        // Add class methods
        if ((null !== $methods) && \is_array($methods) && !empty($methods)) {
            foreach ($methods as $value) {
                $parts[] = '';
                if (($value instanceof PHPClassMethod) || method_exists($value, 'addToNamespace')) {
                    $value = $value->{'addToNamespace'}($blueprint->getNamespace());
                }
                $parts[] = $value->setGlobalImports($imports)->setIndentation("\t")->__toString();
                $imports = array_merge($imports, $value->getImports() ?? []);
            }
        }
        $blueprint->setGlobalImports($imports);
        $parts[] = '';
        $parts[] = '}';

        return implode(\PHP_EOL, $parts);
    }

    protected function buildNamespaceClass(string $namespace, Blueprint $blueprint)
    {
        $classString = $this->compile($blueprint);
        $parts[] = (new PHPNamespace($namespace))
            ->addClass($blueprint)
            ->addImports($blueprint->getGlobalImports() ?? [])->__toString();
        $parts[] = $classString;

        return implode(\PHP_EOL, $parts);
    }
}
