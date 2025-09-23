<?php

namespace App\Xendit;

class Invoice
{
    use ApiOperations\Request;
    use ApiOperations\Create;
    use ApiOperations\Retrieve;
    use ApiOperations\RetrieveAll;

    public static function classUrl()
    {
        return "/v2/invoices";
    }

    public static function createReqParams()
    {
        return ['external_id', 'amount'];
    }

    public static function expireInvoice($id, $params=[])
    {
        $url =  '/invoices/' . $id . '/expire!';

        return static::_request('POST', $url, $params);
    }
}
