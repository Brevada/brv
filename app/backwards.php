<?php
/**
 * Backwards Compatibility
 *
 * Functions and scripts to provide backwards compatibility for older PHP
 * versions.
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

// >= 5.6
if (!function_exists('hash_equals')) {
    /**
     * hash_equals
     * @param  string $str1 First string.
     * @param  string $str2 Second string.
     * @return boolean
     */
    function hash_equals($str1, $str2)
    {
        if (strlen($str1) != strlen($str2)) {
            return false;
        } else {
            $res = $str1 ^ $str2;
            $ret = 0;
            for ($i = strlen($res) - 1; $i >= 0; $i--) {
                $ret |= ord($res[$i]);
            }
            return !$ret;
        }
    }
}
