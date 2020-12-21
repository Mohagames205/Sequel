<?php

namespace Mohamed205\Sequel\query;

use Mohamed205\Sequel\Hooker;
use Mohamed205\Sequel\model\Model;
use Mohamed205\Sequel\model\ModelRegistrar;

class Builder
{

    private $model;

    private $conditions;

    private $operators = [
        "=", "!=", "<", ">", "<=", ">="
    ];

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    // naam, mohamed
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        var_dump($boolean);
        // Column is een array met key kolom en value
        if(is_array($column))
        {
            return $this->pushWhere($column, $boolean);
        }

        if ($this->invalidOperator($operator)) {
            [$value, $operator] = [$operator, '='];
        }

        $type = 'Basic';
        $this->conditions[] = compact('type', 'column', 'operator', 'value', 'boolean');

        return $this;
    }

    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, "or");
    }

    public function invalidOperator(?string $operator): bool
    {
        return !in_array($operator, $this->operators);
    }


    public function pushWhere($column, $boolean, $method = 'where')
    {
        foreach($column as $key => $value)
        {
            $this->$method($key, '=', $value, $boolean);
        }
        return $this;
    }

    public function firstOrFail()
    {
        return $this->executeQuery()[0];
    }

    public function all(): array
    {
        return $this->executeQuery();
    }

    public function executeQuery()
    {
        $reflectionClass = new \ReflectionClass($this->model);
        $shortName = $reflectionClass->getShortName();
        $query = "SELECT * FROM $shortName WHERE";

        $main = ModelRegistrar::getRegisteredModels()->key(get_class($this->model));


        /** @var \SQLite3 $database */
        $database = Hooker::getInstance()->getDatabase($main);


        // TODO: DIT WERKEND MAKEN
        var_dump($this->conditions);
        $i = 0;
        foreach ($this->conditions as $condition)
        {
            $bool = !is_null($this->getPrevValue($this->conditions, $i)) ? $this->getPrevValue($this->conditions, $i)["boolean"] :  "";

            $query .= " " . $condition["column"] . " " . $condition["operator"] . " :" . $condition["column"] . $i . " " .  $bool;
            $i++;
        }

        $stmt = $database->prepare($query);
        $i = 1;
        foreach ($this->conditions as $condition)
        {
            $stmt->bindParam( $i, $condition["value"]);
            $i++;
        }

        $results = [];
        $className = get_class($this->model);
        $res = $stmt->execute();

        while ($row = $res->fetchArray(SQLITE3_ASSOC))
        {
            $modelBuilder = new $className();
            foreach ($row as $column => $value) {
                $modelBuilder->{$column} = $value;
            }
            $results[] = $modelBuilder;
        }

        return $results;
    }

    public function getPrevValue(array $array, int $currentIndex)
    {
        return $array[$currentIndex + 1] ?? null;
    }
}