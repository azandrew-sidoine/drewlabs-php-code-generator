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

namespace Drewlabs\CodeGenerator\Models\Traits;

use Drewlabs\CodeGenerator\Contracts\ValueContainer as ContractsValueContainer;
use Drewlabs\CodeGenerator\Converters\PHPValueStringifier;
use Drewlabs\CodeGenerator\Helpers\Str;

use function Drewlabs\CodeGenerator\Proxy\CommentFactory;

use Drewlabs\CodeGenerator\Types\PHPTypes;

trait ValueContainer
{
    /**
     * List of imports to append to the file/class imports.
     *
     * @var string[]
     */
    private $imports;

    /**
     * PHP Stringeable component.
     *
     * @var mixed
     */
    private $comment;

    /**
     * The default value to set the property to.
     *
     * @var string|array|null
     */
    private $value;

    /**
     * Indicates that the property is a constant property.
     *
     * @var bool
     */
    private $isConstant = false;

    public function value($value = null)
    {
        // Act like a property getter when nothing is passed
        if (null === $value) {
            return $this->value;
        }
        $this->value = $value;
        return $this;
    }

    public function asConstant()
    {
        $this->isConstant = true;

        return $this;
    }

    public function equals(ContractsValueContainer $value)
    {
        return $this->name === $value->getName();
    }

    protected function prepare()
    {
        $type = $this->type();
        if ((null !== $type) && Str::contains($type, '\\')) {
            $this->setType($this->addClassPathToImportsPropertyAfter(function ($classPath) {
                return $this->getClassFromClassPath($classPath);
            })($type));
        }

        return $this;
    }

    protected function setComments()
    {
        $type = $this->type();
        /**
         * @var string[]
         */
        $descriptors = $this->comments();
        if (!empty($descriptors)) {
            // Add a line separator between the descriptors and other definitions
            $descriptors[] = '';
        }
        $this->comment = (CommentFactory(true))->make(
            !empty($descriptors) ?
                ($type ? array_merge(
                    $descriptors ?? [],
                    [
                        "@var $type",
                    ]
                ) : array_merge(
                    $descriptors ?? [],
                    [
                        '@var mixed',
                    ]
                )) : ($type ? [
                    "@var $type",
                ] : [
                    '@var mixed',
                ])
        );

        return $this;
    }

    private function parsePropertyValue()
    {
        return PHPValueStringifier::new($this->getType(), $this->getIndentation())->stringify($this->value);
    }
}
