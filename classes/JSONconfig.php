<?php
use Noodlehaus\Config;
use Noodlehaus\Parser\Json;

//vypůjčeno =)
class JSONconfig
{
    private static ?Config $instance = null;

    public static function getJSONinfo(string $key) : mixed
    {
        return  self::$instance == null ?  self::initialize() : self::$instance->get($key);
    }

    private static function initialize() : void
    {
        $path = __DIR__ . "/../config/";
        self::$instance = Config::load([
            $path . 'config.json',
            $path . 'localConfig.json'
        ]);
    }
}