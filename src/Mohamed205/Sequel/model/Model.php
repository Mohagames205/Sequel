<?php


namespace Mohamed205\Sequelmodel;


use Mohamed205\Sequel\Hooker;
use Mohamed205\Sequel\model\ModelRegistrar;

abstract class Model
{

    public static function where($tables, $condition)
    {
        $className = static::class;
        $modelReflection = new \ReflectionClass($className);
        $tableName = $modelReflection->getShortName();

        $main = ModelRegistrar::getRegisteredModels()->key($className);

        /** @var \SQLite3 $database */
        $database = Hooker::getInstance()->getDatabase($main);

        $query = "SELECT * FROM $tableName WHERE";
        if(is_array($tables))
        {
            foreach ($tables as $tableName => $tableCondition)
            {
                $query .= "$tableName = :$tableName";
            }

            $stmt = $database->prepare($query);

            foreach ($tables as $tableName => $tableCondition)
            {
                $stmt->bindParam($tableName, $tableCondition);
            }

            $res = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
            // TODO

            $modelClass = new $className();
            foreach ($res as $column)
            {
                $modelClass->{$column} = $res;
            }

            return $modelClass;

        }

    }

}