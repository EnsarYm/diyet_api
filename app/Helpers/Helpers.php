<?php

namespace App\Helpers;

class Helpers
{
    public static function shout(string $string)
    {
        return strtoupper($string);
    }

    public static function myCrypt($value)
    {
        $key = "25629974172281039361045933848971";
        $iv = "9495109947225892";
        $encrypted_data = openssl_encrypt($value, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($encrypted_data);
    }

    public static function myDecrypt($value)
    {
        $key = "25629974172281039361045933848971";
        $iv = "9495109947225892";
        $value = base64_decode($value);
        $data = openssl_decrypt($value, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        return $data;
    }
}
