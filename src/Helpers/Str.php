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

use RuntimeException;

class Str
{
    /**
     * PHP String helper function check if it starts with a given value
     * 
     * @param string $haystack 
     * @param string $needle 
     * @return bool 
     */
    public static function startsWith(string $haystack, string $needle)
    {
        return ('' === $needle) || (mb_substr($haystack, 0, mb_strlen($needle)) === $needle);
    }

    /**
     * PHP string helper function check if it ends with the provided needle value
     * 
     * @param string $haystack 
     * @param string $needle 
     * @return bool 
     */
    public static function endsWith(string $haystack, string $needle)
    {
        return ('' === $needle) || (mb_substr($haystack, -(int) (mb_strlen($needle))) === $needle);
    }

    /**
     * Checks if string contains needle substring
     * 
     * @param null|string $haystack 
     * @param mixed $needle
     * 
     * @return bool 
     */
    public static function contains(?string $haystack, $needle)
    {
        if (null === $haystack) {
            return false;
        }
        // Code patch for searching for string directly without converting it to an array of character
        if (is_string($needle)) {
            return '' !== $needle && false !== mb_strpos($haystack, $needle);
        }
        foreach ((array) $needle as $n) {
            if ('' !== $n && false !== mb_strpos($haystack, $n)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Split string into array of component using the delimiter
     * 
     * @param string $value 
     * @param string $delimiter 
     * @return string[] 
     * @throws RuntimeException 
     */
    public static function split(string $value, $delimiter = ',')
    {
        if (!is_string($value)) {
            throw new \RuntimeException('Error parsing value... Provides a string value as parameter');
        }
        return explode($delimiter, (string) $value);
    }

    /**
     * Convert provided word into camel case.
     * If $capitalize_first_chr === true, It capitalize the first character of the
     * generated string as well.
     *
     * @param bool   $firstcapital
     * @param string $delimiter
     *
     * @return string
     */
    public static function camelize(string $haystack, $firstcapital = true, string $delimiter = '_')
    {
        $search_replace = static function (string $h, string $d) {
            return str_replace($d, '', ucwords($h, $d));
        };
        $first_capital = static function ($param) use ($firstcapital) {
            return !$firstcapital ? lcfirst($param) : $param;
        };
        return $first_capital($search_replace($haystack, $delimiter));
    }

    /**
     * Returns uppercase representation of the string
     * 
     * @param string $value 
     * @return string 
     */
    public static function upper(string $value)
    {
        return \function_exists('mb_strtoupper') ? mb_strtoupper($value) : strtoupper($value);
    }

    /**
     * Returns lowercase representation of the string
     * 
     * @param string $value 
     * @return string 
     */
    public static function lower(string $value)
    {
        return \function_exists('mb_strtolower') ? mb_strtolower($value) :  strtolower($value);
    }

    /**
     * Reverse the string object and find the needle position
     * 
     * @param string $haystack 
     * @param string $needle 
     * @return int|float|false 
     */
    public static function strrevpos(string $haystack, string $needle)
    {
        $rev_pos = mb_strpos(strrev($haystack), strrev($needle));
        if (false === $rev_pos) {
            return false;
        }
        return mb_strlen($haystack) - $rev_pos - mb_strlen($needle);
    }

    /**
     * Returns the string after the last occurence of the provided character.
     * 
     * @param string $character 
     * @param string $haystack 
     * @return string|void 
     */
    public static function afterLast(string $character, string $haystack)
    {
        if (!\is_bool($result = self::strrevpos($haystack, $character))) {
            return mb_substr($haystack, $result + mb_strlen($character));
        }
    }
}
