<?php


namespace Mohamed205\Sequel\map;


use pocketmine\plugin\Plugin;

class PluginMap
{
    private $pluginList = [];
    private $linkedPluginlist = [];

    public function exists(Plugin $plugin)
    {
        return in_array(get_class($plugin), $this->pluginList);
    }

    public function value(Plugin $plugin)
    {
        return $this->pluginList[get_class($plugin)];
    }

    public function key($value) : Plugin
    {
        $key = array_search($value, $this->pluginList);
        return $this->linkedPluginlist[$key];
    }

    public function append(Plugin $plugin, $value) : bool
    {
        if(!$this->exists($plugin))
        {
            $this->linkedPluginlist[get_class($plugin)] = $plugin;
            $this->pluginList[get_class($plugin)] = $value;
            return true;
        }
        return false;
    }

    public function pop(Plugin $plugin) : bool
    {
        if($this->exists($plugin)) {
            unset($this->linkedPluginlist[get_class($plugin)]);
            unset($this->pluginList[get_class($plugin)]);
            return true;
        }
        return false;
    }
}