<?php

if (!function_exists('removeSpecialChars')) {

    function removeSpecialChars(string $string)
    {

        $string = str_replace(' ', '-', $string);

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    }
}

if (!function_exists('censorText')) {

    /**
     * Function to replace characters in string with "*"
     * 
     * How to use:
     * censorText('owaieofawdokjda', 6) => 'owa***jda'
     * 
     * @param string $string        Input string
     * @param int $showTextLength   Length of chars that want to display
     * @return string
     */
    function censorText(string $string, int $showTextLength)
    {

        $showTextLength = abs($showTextLength);

        if (
            $showTextLength < strlen($string)
            || $showTextLength >= 2
        ) {

            $show = [
                'start' => ceil(($showTextLength) / 2),
                'end' => floor(($showTextLength) / 2)
            ];

            $returnText = '';

            for ($i = 0; $i < strlen($string); $i++) {

                if ($i + 1 <= $show['start']) {

                    $returnText .= $string[$i];
                } else if ($i + 1 > strlen($string) - $show['end']) {

                    $returnText .= $string[$i];
                } else {

                    $returnText .= '*';
                }
            }

            return $returnText;
        } else if ($showTextLength == 1) {

            return $string[0] . substr($string, 1, strlen($string) - 1);
        } else {

            return $string;
        }
    }
}


if (!function_exists('printPhoneNumber')) {

    function printPhoneNumber(string $region_code, string $change, string $phone_number)
    {
        $length = strlen($region_code);

        if (substr($phone_number, 0, $length) == $region_code) {
            return $change . substr($phone_number, $length);
        }

        return $phone_number;
    }
}
