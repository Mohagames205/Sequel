<?php


namespace Mohamed205\Sequel\model;


use Mohamed205\Sequel\Hooker;
use Mohamed205\Sequel\model\ModelRegistrar;
use Mohamed205\Sequel\query\Builder;
use mohamed205\TDBPin\model\PinRequest;

abstract class Model
{
    protected $tableName;

    protected $isFilled;

    public static function __callStatic(string $name , array $arguments)
    {
        return (new static)->$name(...$arguments);
    }

    public function __call(string $name, array $arguments)
    {
        return $this->initializeBuilder()->$name(...$arguments);
    }

    public function initializeBuilder() : Builder
    {
        return new Builder($this);
    }

    public function __set(string $name , $value)
    {
        $this->{$name} = $value;
    }

    public function save()
    {
        return $this->initializeBuilder()->saveModel();
    }

    public function delete()
    {
        return $this->initializeBuilder()->deleteModel();
    }

    public function isFilled() : bool
    {
        return $this->isFilled;
    }

    public function setFilled(bool $filled = true)
    {
        $this->isFilled = $filled;
    }

    public function getConnection() : \SQLite3
    {
        $main = ModelRegistrar::getRegisteredModels()->key(get_class($this));
        return Hooker::getInstance()->getDatabase($main);
    }

    public function hasOne($modelClass) : Model
    {
        $childReflection = new \ReflectionClass($this);
        $shortName = $childReflection->getShortName();
        $idField = $shortName . "_" . "id";

        if(!$this->isFilled()) throw new \Exception("Non filled model cannot be used to fetch data.");

        return $modelClass::where([$idField => $this->id]);
    }

    public function belongsTo($modelClass) : Model
    {

    }

}