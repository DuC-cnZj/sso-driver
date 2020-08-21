<?php


namespace DucCnzj\Sso;


use GuzzleHttp\Client;

class Http
{
    private static $client;

    public static function instance()
    {
        if (static::$client instanceof Client) {
            return static::$client;
        }

        return static::$client = new Client(['base_uri' => config('sso.base_url')]);
    }

    public static function setInstance(Client $client)
    {
        static::$client = $client;
    }

    private function __construct()
    {
    }

    private function __wakeup()
    {
    }

    private function __clone()
    {
    }
}