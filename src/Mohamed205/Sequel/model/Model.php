<?php


namespace Mohamed205\Sequel\model;


use Mohamed205\Sequel\Hooker;
use Mohamed205\Sequel\model\ModelRegistrar;

abstract class Model
{

    private $tableName;

    public static function where($tables)
    {
        $className = static::class;
        $modelReflection = new \ReflectionClass($className);
        $modelClass = new $className();
        $tableName = $className->tableName ?? strtolower($modelReflection->getShortName());

        $main = ModelRegistrar::getRegisteredModels()->key($className);

        /** @var \SQLite3 $database */
        $database = Hooker::getInstance()->getDatabase($main);

        $query = "SELECT * FROM $tableName WHERE";
        if(is_array($tables))
        {
            foreach ($tables as $tableName => $tableCondition)
            {
                $query .= " $tableName = :$tableName";
            }

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

    public function insert()
    {

    }

    public static function all()
    {

    }

}