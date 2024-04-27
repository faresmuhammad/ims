<?php

use Carbon\Carbon;


if (!function_exists('randomCode')) {
    function randomCode($length = 10)
    {
        $numbers = '0123456789';
        $output = '';

        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($numbers) - 1);
            $output .= $numbers[$index];
        }

        return $output;
    }
}

if (!function_exists('convertStringToDatemmyyyy')) {
    function convertStringToDatemmyyyy(string $string)
    {
        $dateParts = explode('/', $string);
        $month = $dateParts[0];
        $year = $dateParts[1];
        $date = Carbon::createFromDate($year, $month, 1);

        return $date;
    }
}

if (!function_exists('formatCarbonDate')) {
    function formatCarbonDate(?string $date, $format = 'm/Y')
    {
        if ($date == null) return;
        $carbonDate = Carbon::parse($date);
        return $carbonDate->format($format);
    }
}
