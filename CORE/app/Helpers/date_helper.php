<?php

if (!function_exists('appendDate')) {

    /**
     * Function to Add and Subtract Date
     * 
     * How to use:
     * appendDate('2000-12-5', 10) => '2000-12-15'
     * 
     * @param string $date Format: yyyy-mm-dd
     * @param int $append The amount of addition and subtraction of date
     * @return string
     */
    function appendDate(string $date, int $append)
    {

        // Convert date to TTL
        $dtTimestamp = strtotime($date);

        // Times $append with TTL (1 Day)
        $appendTTL = 86400 * $append;

        // Add First TTL with append TTL
        $finalDtTimestamp = $dtTimestamp + $appendTTL;

        // Return string in y-m-d format
        return date('Y-m-d', $finalDtTimestamp);
    }
}

if (!function_exists('appendMin')) {

    function appendMin(
        string $dateNow,
        int $append
    ) {

        $date = explode(' ', $dateNow)[0];
        $time = explode(':', explode(' ', $dateNow)[1]);
        $finalTime = null;

        while (1) {

            if ($append > 0) {

                // If append value is positive
                if (intval($time[1]) + $append >= 60) {

                    $append -= (60 - intval($time[1]));

                    $time[1] = '00';

                    if (intval($time[0]) + 1 >= 24) {

                        $time[0] = '00';

                        // Append date
                        $date = appendDate($date, 1);
                    } else {

                        $time[0] = intval($time[0]) + 1;

                        $time[0] = str_pad($time[0], 2, '0', STR_PAD_LEFT);
                    }
                } else {

                    $time[1] = intval($time[1]) + $append;

                    $time[1] = str_pad($time[1], 2, '0', STR_PAD_LEFT);

                    $append = 0;
                }
            } else if ($append < 0) {

                // If append value is negative
                if ($append + intval($time[1]) <= 0) {

                    $append += intval($time[1]);

                    $time[1] = '59';

                    if (intval($time[0]) - 1 < 0) {

                        $time[0] = '23';

                        $date = appendDate($date, -1);
                    } else {

                        $time[0] = intval($time[0]) - 1;

                        $time[0] = str_pad($time[0], 2, '0', STR_PAD_LEFT);
                    }
                } else {

                    $time[1] = intval($time[1]) + $append;

                    $time[1] = str_pad($time[1], 2, '0', STR_PAD_LEFT);

                    $append = 0;
                }
            } else {

                $finalTime = $date . ' ' . $time[0] . ':' . $time[1] . ':' . $time[2];

                break;
            }
        }

        // Return
        return $finalTime;
    }
}

if (!function_exists('convertMonth')) {

    function convertMonth(
        $month,
        String $format = 'FULL',
        String $lang = 'ID'
    ) {

        $month_name = [];

        switch ($lang) {

            case 'ID':

                $month_name = [
                    'januari', 'februari', 'maret',
                    'april', 'mei', 'juni',
                    'juli', 'agustus', 'september',
                    'oktober', 'november', 'desember'
                ];
                break;

            case 'EN':
            default:

                $month_name = [
                    'january', 'february', 'march',
                    'april', 'may', 'june',
                    'july', 'august', 'september',
                    'october', 'november', 'desember'
                ];
                break;
        }

        if ($format == 'FULL') {

            return $month_name[(intval($month) - 1)];
        } else if ($format == 'SHORT') {

            return substr($month_name[(intval($month) - 1)], 0, 3);
        }
    }
}

if (!function_exists('convertYmdhi')) {

    // Hours to TTL
    function convertYmdhi(
        $date = null,
        String $format = 'FULL',
        String $lang = 'ID'
    ) {

        if ($date == null) return null;

        $splitDate = explode(' ', $date);
        $exp = explode('-', $splitDate[0]);

        return $exp[2] . ' ' . ucfirst(convertMonth($exp[1], $format, $lang)) . ' ' . $exp[0] . ', ' . substr($splitDate[1], 0, 5);
    }
}

if (!function_exists('convertYmd')) {

    // Hours to TTL
    function convertYmd(
        String $date = null,
        String $format = 'FULL',
        String $lang = 'ID'
    ) {

        if ($date == null) return null;

        $exp = explode('-', $date);

        return $exp[2] . ' ' . ucfirst(convertMonth($exp[1], $format, $lang)) . ' ' . $exp[0];
    }
}
