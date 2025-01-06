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

use Drewlabs\CodeGenerator\Contracts\Stringable;
use Drewlabs\CodeGenerator\DocComments\Keywords;
use Drewlabs\CodeGenerator\Helpers\Str;
use Drewlabs\CodeGenerator\Models\Traits\HasIndentation;

class MultiLinePHPComment implements Stringable
{
    use HasIndentation;

    /**
     * List of descriptions.
     *
     * @var array
     */
    private $descriptors;

    public function __construct(array $descriptors = [])
    {
        $this->descriptors = $descriptors;
    }

    public function __toString(): string
    {
        $start = '/**';
        $parts[0] = $start;
        foreach (($this->getDescriptors() ?? []) as $key => $value) {
            $addLine = Str::contains($value, Keywords::THROWS) || Str::contains($value, Keywords::RETURNS);
            if ($addLine) {
                $parts[] = $this->getIndentation() ? $this->getIndentation().' *' : ' *';
            }
            $parts[] = $this->getIndentation() ? sprintf('%s * %s', $this->getIndentation(), $value) : sprintf(' * %s', $value);
        }
        $parts[] = $this->getIndentation() ? sprintf('%s */', $this->getIndentation()) : ' */';

        return implode(\PHP_EOL, $parts);
    }

    /**
     * Set the comment descriptors that will compose the comment.
     *
     * @return self
     */
    public function setDescriptors(array $descriptors)
    {
        $this->descriptors = $descriptors;

        return $this;
    }

    /**
     * Returns the list of descriptors define on the comment.
     *
     * @return array
     */
    public function getDescriptors()
    {
        return $this->descriptors;
    }
}
