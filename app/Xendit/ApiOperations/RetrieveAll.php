<?php

namespace App\Xendit\ApiOperations;

trait RetrieveAll
{
    public static function retrieveAll($params = [])
    {
        $url = static::classUrl();
        return static::_request('GET', $url, $params);
    }
}
