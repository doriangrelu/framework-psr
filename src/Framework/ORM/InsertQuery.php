<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 03/05/2018
 * Time: 23:18
 */

namespace App\Framework\ORM;


use Rhumsaa\Uuid\Exception\UnsupportedOperationException;

class InsertQuery extends Query
{


    /**
     * @var array
     */
    private $values = [];

    /**
     * @var String
     */
    private $table;

    private $valuesInsert = [];

    /**
     * InsertQuery constructor.
     * @param \PDO $pdo
     * @param string $table
     * @param array $values
     */
    public function __construct(\PDO $pdo, string $table, array $values)
    {
        parent::__construct($pdo);
        $this->table = $table;
        $this->values = $values;
    }

    public function save()
    {
        if($this->pdo->prepare($this->__toString())->execute($this->valuesInsert)){
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    public function __toString()
    {
        $escape = [];
        $fields = [];
        foreach ($this->values as $field => $value) {
            $this->valuesInsert[$field] = $value;
            $fields[] = "`$field`";
            $escape[] = ":$field";
        }
        return "INSERT INTO `{$this->table}` (".implode(',', $fields).") VALUES (".implode(',', $escape).")";
    }


}