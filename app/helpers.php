<?php


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

