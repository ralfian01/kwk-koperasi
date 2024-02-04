<?php

if (!function_exists('decode_stdClass')) {

    /**
     * Function to decode stdClass
     * 
     * @param stdClass $stdClass
     * @return array
     */
    function decode_stdClass(stdClass $stdClass): array
    {

        return (array) json_decode(json_encode($stdClass), true);
    }
}

if (!function_exists('is_string_base64')) {

    /**
     * Function to check if string is base64 or not
     * 
     * How to use: 
     * is_string_base64('owhih129eh') => False
     * 
     * @param string $string
     * @return boolean
     */
    function is_string_base64(string $string = ''): bool
    {

        return (bool) base64_encode(base64_decode($string, true)) === $string;
    }
}

if (!function_exists('var_isset')) {

    /**
     * Function to check if variable available or not and print default string if variable not available
     * 
     * @param variable &$var
     * @param mixed $alter_val  Alternative value when variable not available
     * @return mixed
     */
    function var_isset(&$var, $alter_val = '')
    {

        if (!isset($var)) return $alter_val;
        return $var;
    }
}

if (!function_exists('full_current_url')) {

    function full_current_url()
    {

        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
}

if (!function_exists('last_item')) {

    function last_item(array $array)
    {

        return $array[count($array) - 1];
    }
}

if (!function_exists('build_url')) {

    function build_url(array $urlComponent)
    {

        $buildUrl = '';

        if (isset($urlComponent['scheme'])) $buildUrl .= $urlComponent['scheme'] . '://';
        if (isset(
            $urlComponent['user'],
            $urlComponent['pass']
        )) $buildUrl .= $urlComponent['user'] . ':' . $urlComponent['pass'] . '@';
        if (isset($urlComponent['host'])) $buildUrl .= $urlComponent['host'];
        if (isset($urlComponent['port'])) $buildUrl .= ':' . $urlComponent['port'];
        if (isset($urlComponent['path'])) $buildUrl .= $urlComponent['path'];
        if (isset($urlComponent['query'])) $buildUrl .= '?' . $urlComponent['query'];
        if (isset($urlComponent['fragment'])) $buildUrl .= '#' . $urlComponent['fragment'];

        return $buildUrl;
    }
}

if (!function_exists('strpos2')) {

    // Round number
    function strpos2(string $string, $find = null)
    {

        if ($find == null) return false;

        if (!is_array($find)) $find[] = $find;

        foreach ($find as $value) {

            if (strpos($string, $value)) return true;
        }

        return false;
    }
}

if (!function_exists('cookie_expires')) {

    // Cookie expired in hour
    function cookie_expires(int $int)
    {

        return time() + 3600 * $int;
    }
}

if (!function_exists('getcookie2')) {

    // Hours to TTL
    function getcookie2($cookie_name = null)
    {

        if ($cookie_name == null) return null;

        return isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : null;
    }
}
