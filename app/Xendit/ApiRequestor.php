<?php

namespace App\Xendit;

class ApiRequestor
{
    private static $_httpClient;

    public function request($method, $url, $params = [], $headers = [])
    {
        list($rbody, $rcode, $rheaders)
            = $this->_requestRaw($method, $url, $params, $headers);

        return json_decode($rbody, true);
    }

    private function _setDefaultHeaders($headers)
    {
        $defaultHeaders = [];
        $lib = 'php';
        $libVersion = Xendit::getLibVersion();

        $defaultHeaders['Content-Type'] = 'application/json';
        $defaultHeaders['xendit-lib'] = $lib;
        $defaultHeaders['xendit-lib-ver'] = $libVersion;

        return array_merge($defaultHeaders, $headers);
    }

    private function _requestRaw($method, $url, $params, $headers)
    {
        $defaultHeaders = self::_setDefaultHeaders($headers);

        $response = $this->_httpClient()->sendRequest(
            $method,
            $url,
            $defaultHeaders,
            $params
        );

        $rbody = $response[0];
        $rcode = $response[1];
        $rheaders = $response[2];

        return [$rbody, $rcode, $rheaders];
    }

    private function _httpClient()
    {
        if (!self::$_httpClient) {
            self::$_httpClient = HttpClient\GuzzleClient::instance();
        }
        return self::$_httpClient;
    }

    public static function setHttpClient($client)
    {
        self::$_httpClient = $client;
    }
}
