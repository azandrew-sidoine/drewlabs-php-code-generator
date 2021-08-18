<?php

namespace Drewlabs\CodeGenerator\Helpers;

class PHPLanguageDefifinitions
{

    /**
     * Helper method for identifying PHP block of expressions
     *
     * @param string $line
     * @return boolean
     */
    public static function isBlock(string $line)
    {
        return !empty($line) &&
            (drewlabs_core_strings_starts_with(trim($line), '{') ||
                drewlabs_core_strings_ends_with(trim($line), '{') ||
                drewlabs_core_strings_starts_with(trim($line), '}') ||
                drewlabs_core_strings_ends_with(trim($line), '}'));
    }

    /**
     * Helper method for identifying PHP comments
     * 
     * @param string $line 
     * @return bool 
     */
    public static function isComment(string $line)
    {
        return !empty($line) &&
            (drewlabs_core_strings_starts_with(trim($line), '*') ||
                drewlabs_core_strings_starts_with(trim($line), '/*') ||
                drewlabs_core_strings_ends_with(trim($line), '*/') ||
                drewlabs_core_strings_starts_with(trim($line), '//'));
    }
}
