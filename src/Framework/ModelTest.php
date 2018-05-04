<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 01/05/2018
 * Time: 18:57
 */

namespace Framework;


use App\Framework\ORM\Entity;
use App\Framework\ORM\InsertQuery;
use App\Framework\ORM\SelectQuery;
use Framework\Database\Query;

class ModelTest
{

    /**
     * @var \PDO
     */
    private $pdo;

    protected $table;

    /**
     * @var Entity
     */
    private $entity;

    private $fields = [];

    public function __get($name)
    {
        return $this->fields[$name] ?? null;
    }

    public function __set($name, $value)
    {
        $this->fields[$name] = $value;
    }

    /**
     * Model constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->entity = $this;
        $this->pdo = $pdo;
    }

    /**
     * Select element
     * @param string ...$fields
     * @return SelectQuery
     */
    public function select(string...$fields): SelectQuery
    {
        if(count($fields)===0){
            $fields = ["{$this->table}.*"];
        }
        return (new SelectQuery($this->pdo, $this->table, $fields, $this->entity));
    }

    public function insert(array $values):InsertQuery
    {
        return (new InsertQuery($this->pdo, $this->table, $values));
    }

}