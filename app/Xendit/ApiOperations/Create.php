<?php

namespace App\Xendit\ApiOperations;

trait Create
{
    public static function create($params = [])
    {
        self::validateParams($params, static::createReqParams());

        $url = static::classUrl();

        return static::_request('POST', $url, $params);
    }
}
