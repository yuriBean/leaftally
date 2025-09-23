<?php

namespace App\Xendit\ApiOperations;

trait Update
{
    public static function update($id, $params = [])
    {
        self::validateParams($params, static::updateReqParams());

        $url = static::classUrl() . '/' . $id;

        return static::_request('PATCH', $url, $params);
    }
}
