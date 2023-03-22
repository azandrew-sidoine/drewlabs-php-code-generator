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

namespace Drewlabs\CodeGenerator\Helpers;

use Closure;

class Arr
{

    /**
     * Performs a binary search on the provided array
     * 
     * @param array $haystack 
     * @param mixed $value 
     * @param null|Closure $predicate 
     * @param null|int $start 
     * @param null|int $end 
     * @return int 
     */
    public static function bsearch(array $haystack, $value = null, ?\Closure $predicate = null, ?int $start = null, ?int $end = null)
    {
        $start = $start ?? 0;
        $end = $end ?? (\count($haystack) - 1);
        $predicate = $predicate ?? static function ($source, $match) {
            if ($source === $match) {
                return 0;
            }
            if ($source > $match) {
                return -1;
            }

            return 1;
        };
        while ($start <= $end) {
            $mid = (int) (ceil($start + ($end - $start) / 2));
            $result = $predicate($haystack[$mid], $value);
            if (0 === $result) {
                return $mid;
            }
            if (-1 === $result) {
                $end = $mid - 1;
            } else {
                $start = $mid + 1;
            }
        }

        return -1;
    }


    /**
     * Return the last key of a php array.
     * 
     * @param array $list 
     * @return string|int|null 
     */
    public static function keyLast(array $list)
    {
        return \function_exists('array_key_last') ? array_key_last($list) : (!empty($list) ? key(\array_slice($list, -1, 1, true)) : null);
    }
}
