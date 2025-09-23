<?php

namespace App\Xendit;

use Dotenv\Dotenv;

class Xendit
{
    public static $apiKey;

    public static $apiBase = 'https://api.xendit.co';

    public static $libVersion;

    private static $_httpClient;

    const VERSION = "2.18.0";

    public static function getApiBase(): string
    {
        return self::$apiBase;
    }

    public static function setApiBase(string $apiBase): void
    {
        self::$apiBase = $apiBase;
    }

    public static function getApiKey()
    {
        return self::$apiKey;
    }

    public static function setApiKey($apiKey)
    {
        self::$apiKey = $apiKey;
    }

    public static function getLibVersion()
    {
        if (self::$libVersion === null) {
            self::$libVersion = self::VERSION;
        }
        return self::$libVersion;
    }

    public static function setLibVersion($libVersion = null): void
    {
        self::$libVersion = $libVersion;
    }

    public static function setHttpClient(HttpClientInterface $client): void
    {
        self::$_httpClient = $client;
    }

    public static function getHttpClient()
    {
        return self::$_httpClient;
    }
}
