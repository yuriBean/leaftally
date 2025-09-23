<?php

namespace App\Xendit\HttpClient;

use App\Xendit\Exceptions\ApiException;

interface ClientInterface
{
    public function sendRequest($method,
        string $url,
        array $defaultHeaders,
        $params
    );
}
