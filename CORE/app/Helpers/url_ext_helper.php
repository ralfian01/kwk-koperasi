<?php

use MVCME\Service\Services;
use MVCME\URI\URI;

if (!function_exists('member_url')) {

    /**
     * Function to get member url in string
     * @return string
     */
    function member_url(?string $path = null)
    {
        $config = Services::appConfig();

        $scheme = $config->secureRequest ? 'https' : 'http';

        $hostname = $_SERVER['member_hostname'] ?? '';

        if ($path != null) {
            $path = ltrim($path, '/');
            $path = "/{$path}";
        }

        return "{$scheme}://{$hostname}{$path}";
    }
}

if (!function_exists('set_member_url')) {

    /**
     * Function to get set member url in string
     * @return void
     */
    function set_member_url(string $hostname)
    {
        $url = Services::normalizeURI($hostname);

        $_SERVER['member_hostname'] ??= $url;
    }
}

if (!function_exists('clean_current_url')) {

    /**
     * Function to get cleant current url in string
     * @return string
     */
    function clean_current_url(?string $path = null)
    {
        $request ??= Services::request();
        /** @var HTTPRequest $request */
        $uri = $request->getUri();

        $config = Services::appConfig();
        $scheme = $config->secureRequest ? 'https' : 'http';

        $uriString = URI::createURIString($scheme, $uri->getAuthority(), $uri->getPath());
        return str_replace('index.php/', '', $uriString) . $path;
    }
}
