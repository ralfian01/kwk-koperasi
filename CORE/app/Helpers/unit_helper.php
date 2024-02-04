<?php

if (!function_exists('roundNumber')) {

    /**
     * Function to round number
     * 
     * @param int $number
     * @return int
     */
    function roundNumber(int $number)
    {

        $number = number_format($number, 1, '.', '');

        $expNumber = explode('.', $number);
        $expNumber[1] >= 5 ? $expNumber[1] = '5' : $expNumber[1] = '0';

        $number = $expNumber[0] . '.' . $expNumber[1];

        return $number;
    }
}

if (!function_exists('roundUnit')) {

    /**
     * Function to round a number with its unit
     * 
     * @param int $number   Number input
     * @param string $unit  WEIGHT|LENGTH|QUANTITY
     * @return string
     */
    function roundUnit(int $number, string $unit)
    {

        $unit = strtoupper($unit);

        $unitCode = [
            'WEIGHT' => ['gr', 'kg', 'kg', 'kg'],
            'LENGTH' => ['m', 'km', 'km', 'km'],
            'QUANTITY' => ['', 'rb', 'jt', 'mly']
        ];

        if ($number >= 1000000000) {

            // More than 1 billion
            $number = substr(strval($number), 0, -9);
            $unit = $unitCode[$unit][3];
        } else if ($number >= 1000000) {

            // More than 1 million
            $number = substr(strval($number), 0, -6);
            $unit = $unitCode[$unit][2];
        } else if ($number >= 1000) {

            // More than 1 kilo
            $number = substr(strval($number), 0, -3);
            $unit = $unitCode[$unit][1];
        } else {

            // Less than 1 kilo
            $number = strval($number);
            $unit = $unitCode[$unit][0];
        }

        return $number . $unit;
    }
}
