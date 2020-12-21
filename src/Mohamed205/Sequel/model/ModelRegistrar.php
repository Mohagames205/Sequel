<?php


namespace Mohamed205\Sequel\model;


use Mohamed205\Sequel\map\PluginMap;
use pocketmine\plugin\Plugin;

class ModelRegistrar
{
    /**
     * @var PluginMap
     */
    private static $registeredModels;

    public static function init()
    {
        self::$registeredModels = new PluginMap();
    }

    public static function register(string $className, Plugin $plugin)
    {
        // TODO check if class actually exists
        if(!self::$registeredModels->append($plugin, $className)){
            throw new \Exception("Model is already registered");
        }
    }

    public static function getRegisteredModels()
    {
        return self::$registeredModels;
    }





}