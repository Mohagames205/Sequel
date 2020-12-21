<?php


namespace Mohamed205\Sequel\model;


use Mohamed205\Sequel\Hooker;
use Mohamed205\Sequel\model\ModelRegistrar;
use Mohamed205\Sequel\query\Builder;

abstract class Model
{

    protected $tableName;

    public static function wherregerge($tables)
    {
        $className = static::class;
        $modelReflection = new \ReflectionClass($className);
        $modelClass = new $className();
        $tableName = $className->tableName ?? strtolower($modelReflection->getShortName());

        $main = ModelRegistrar::getRegisteredModels()->key($className);

        /** @var \SQLite3 $database */
        $database = Hooker::getInstance()->getDatabase($main);

        $query = "SELECT * FROM $tableName WHERE";
        $conditionList = [];
        if(is_array($tables))
        {
            foreach ($tables as $tableName => $tableCondition)
            {
                $conditionList[] = " $tableName = :$tableName";
            }

            $conditionQuery = join(' AND', $conditionList);
            $query .= $conditionQuery;
            $stmt = $database->prepare($query);

            foreach ($tables as $tableName => $tableCondition)
            {
                $stmt->bindParam($tableName, $tableCondition);
            }

            $res = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

            if(!$res) return null;

            foreach ($res as $column => $value)
            {
                $modelClass->{$column} = $value;
            }

            return $modelClass;
        }
    }

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

    public function insert()
    {

    }

    public static function all()
    {

    }

}