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

function createDateFromFormat(string $date, string $format)
{
    return Carbon::createFromFormat($format, $date);
}

function createDateFromExpireFormat(string $date)
{
    return createDateFromFormat($date, 'm/Y');
}

function formatExpireDate(?string $date)
{
    if ($date == null)
        return null;
    $carbonDate = Carbon::parse($date);
    return $carbonDate->format('m/Y');
}
