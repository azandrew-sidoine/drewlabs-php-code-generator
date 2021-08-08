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
use Drewlabs\CodeGenerator\Contracts\ImplementableStruct;
use Drewlabs\CodeGenerator\Models\PHPNamespace;

final class PHPInterfaceConverter implements Stringifier
{
    /**
     * {@inheritDoc}
     *
     * @param ImplementableStruct $component
     *
     * @throws \InvalidArgumentException
     */
    public function stringify($component): string
    {
        if (!($component instanceof ImplementableStruct)) {
            throw new \InvalidArgumentException(__CLASS__.' only provides '.ImplementableStruct::class.' class stringifier. For other type look for the specific converter.');
        }
        if (null !== ($namespace = $component->getNamespace())) {
            return $this->typeWithNamespaceToString($namespace, $component);
        }

        return $this->typeToString($component);
    }

    private function typeToString(ImplementableStruct $component): string
    {
        // Imports are set in the type stringifier method
        $parts = [];
        $declaration = sprintf('interface %s', $component->getName());
        $baseInterface = $component->getBaseInterface();
        if ((null !== $baseInterface)) {
            $declaration .= sprintf(' extends %s', $baseInterface);
        }
        $parts[] = $declaration;
        $parts[] = '{';
        $imports = $component->getImports();

        // Add interface $methods
        $methods = $component->getMethods();
        if ((null !== $methods) && \is_array($methods) && !empty($methods)) {
            foreach ($methods as $value) {
                $parts[] = '';
                // Call asInterfaceMethod() to indicates that the method is defines on an interface
                $parts[] = $value->asInterfaceMethod()->setGlobalImports($imports)->setIndentation("\t")->__toString();
                $imports = array_merge($imports, $value->getImports() ?? []);
            }
        }
        $component->setGlobalImports($imports);
        $parts[] = '';
        $parts[] = '}';

        return implode(\PHP_EOL, $parts);
    }

    private function typeWithNamespaceToString(string $namespace, ImplementableStruct $component)
    {
        $result = $this->typeToString($component);
        $parts[] = (new PHPNamespace($namespace))
            ->addInterface($component)
            ->addImports($component->getGlobalImports())->__toString();
        $parts[] = $result;

        return implode(\PHP_EOL, $parts);
    }
}
