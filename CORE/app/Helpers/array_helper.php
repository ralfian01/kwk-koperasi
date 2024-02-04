<?php

if (!function_exists('removeNullValues')) {

    /**
     * Fungsi untuk menghapus key dari array asosiatif yang memiliki value null.
     *
     * @param array $array Array asosiatif yang akan dihapus key dengan value null.
     * @return array Array baru tanpa key yang memiliki value null.
     */
    function removeNullValues(array $array)
    {
        return array_filter($array, function ($value) {
            return $value !== null;
        });
    }
}

if (!function_exists('inArrayFound')) {

    /**
     * Check if value exists in array
     * @return bool|int
     */
    function inArrayFound(array $needle, array $haystack)
    {
        $found = 0;

        foreach ($needle as $ndVal) {
            if (in_array($ndVal, $haystack))
                $found++;
        }

        return $found;
    }
}
