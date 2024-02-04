<?php

if (!function_exists('rupiah')) {

    /**
     * Function for formatting number to currency (Rupiah)
     * 
     * How to use:
     * rupiah(1000) => 1.000
     * 
     * @param int $int
     * @return string
     */
    function rupiah(int $int)
    {
        return (string) number_format($int, 0, ',', '.');
    }
}
