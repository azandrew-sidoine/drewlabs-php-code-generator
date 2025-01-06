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

use Drewlabs\CodeGenerator\CommentModelFactory;
use Drewlabs\CodeGenerator\Contracts\Blueprint;
use Drewlabs\CodeGenerator\Contracts\Converters\Stringifier;
use Drewlabs\CodeGenerator\Contracts\HasPHP8Attributes;
use Drewlabs\CodeGenerator\Contracts\ValueContainer;
use Drewlabs\CodeGenerator\Models\PHP8Attribute;
use Drewlabs\CodeGenerator\Models\PHPClassMethod;
use Drewlabs\CodeGenerator\Models\PHPClassProperty;
use Drewlabs\CodeGenerator\Models\PHPNamespace;

class PHPClassConverter implements Stringifier
{
    /** @var bool */
    private $promoteProperties = false;

    /**
     * Class constructor
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
            $parts[] = (string)((new CommentModelFactory(true))->make($blueprint->comments()));
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

        // Add properties
        if ((version_compare(\PHP_VERSION, '8.0.0') >= 0) && !$this->promoteProperties) {
            $properties = $blueprint->getProperties();
            if ((null !== $properties) && \is_array($properties) && !empty($properties)) {
                foreach ($properties as $value) {
                    $parts[] = '';
                    if (($value instanceof PHPClassProperty) || method_exists($value, 'addToNamespace')) {
                        /** @var ValueContainer */
                        $value = $value->{'addToNamespace'}($blueprint->getNamespace());
                    }
                    $parts[] = $value->setIndentation("\t")->__toString();
                    $imports = array_merge($imports, $value->getImports() ?? []);
                }
            }
        }

        // Add class methods
        $methods = $blueprint->getMethods();
        if ((null !== $methods) && \is_array($methods) && !empty($methods)) {
            foreach ($methods as $value) {
                $parts[] = '';
                if (($value instanceof PHPClassMethod) || method_exists($value, 'addToNamespace')) {
                    /** @var ValueContainer */
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
