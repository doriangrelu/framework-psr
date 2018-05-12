<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 12/05/2018
 * Time: 18:45
 */

namespace App\Framework\ORM;


abstract class QueryWithWhere extends Query
{

    private $where = [];

    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
    }

    public function where(string ... $args):self{

        $this->where = array_merge($this->where, $args);
        return $this;
    }

    public abstract function save();

    protected function getWhere():string
    {
        $whereClause = "";

        if (count($this->where)) {
            $whereClause = ' WHERE ' . implode(" AND ", $this->where);
        }

        return $whereClause;
    }

}