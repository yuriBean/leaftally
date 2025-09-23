<?php

namespace App\Xendit\ApiOperations;

trait Retrieve
{
    public static function retrieve($id, $params = [])
    {
        $url = static::classUrl() . '/' . $id;
        return static::_request('GET', $url, $params);
    }
}
