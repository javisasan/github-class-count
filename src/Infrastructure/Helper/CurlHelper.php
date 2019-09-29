<?php

namespace App\Infrastructure\Helper;

class CurlHelper
{
    public static function executeCurl($url)
    {
        $handle = curl_init();

        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

        $data = curl_exec($handle);

        curl_close($handle);

        return json_decode($data, true);
    }
}