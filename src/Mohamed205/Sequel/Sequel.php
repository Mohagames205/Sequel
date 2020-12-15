<?php

declare(strict_types=1);

namespace Mohamed205\Sequel;

use Mohamed205\Sequel\model\ModelRegistrar;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Sequel extends PluginBase{

    use SingletonTrait;

    public function onEnable()
    {
        new Hooker();
        ModelRegistrar::init();
    }


}
