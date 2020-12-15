<?php


namespace Mohamed205\Sequel;


use Mohamed205\Sequel\map\PluginMap;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use SQLite3;

class Hooker
{

    use SingletonTrait;

    /**
     * @var PluginMap
     */
    private $databases;

    public function __construct()
    {
        $this->databases = new PluginMap();
        self::setInstance($this);
    }

    public function hookDatabase(PluginBase $plugin, SQLite3 $database)
    {
        if(!$this->databases->append($plugin, $database)){
            throw new \Exception("Database is already hooked");
        }
    }

    public function getDatabase(Plugin $plugin) : SQLite3
    {
        return $this->databases->value($plugin);
    }


}