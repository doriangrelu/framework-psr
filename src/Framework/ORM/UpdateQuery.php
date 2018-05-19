<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 03/05/2018
 * Time: 23:18
 */

namespace App\Framework\ORM;


class UpdateQuery extends QueryWithWhere
{

    private $table;

    private $where = [];

    private $params = [];
    private $fields = [];

    /**
     * UpdateQuery constructor.
     * @param \PDO $pdo
     * @param string $table
     * @param array $fields
     */
    public function __construct(\PDO $pdo, string $table, array $fields)
    {
        parent::__construct($pdo);
        $this->table = $table;
        $this->fields = $fields;
    }


    public function where(string ...$conditions): self
    {
        $this->where = array_merge($this->where, $conditions);
        return $this;
    }

    public function params(array $params): self
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    public function save()
    {
        $query = $this->pdo->prepare($this->__toString());
        if ($query->execute($this->params)) {
            return $query->rowCount();
        }
        return false;
    }

    public function __toString()
    {
        $fields = [];
        $binded = [];
        foreach ($this->fields as $field => $value) {
            $fields[] = "`$field`";
            $binded[] = "`$field`=:$field";
        }
        $this->withParams($this->fields);
        //$this->params = array_merge($this->params, $this->fields);
        return "UPDATE `$this->table` SET " . implode(', ', $binded) . $this->getWhere();
    }
}