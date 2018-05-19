<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 03/05/2018
 * Time: 23:18
 */

namespace App\Framework\ORM;


class DeleteQuery extends QueryWithWhere
{
    private $table;

    public function __construct(\PDO $pdo, string $table)
    {
        parent::__construct($pdo);
        $this->table = $table;
    }



    public function save()
    {
        $query = $this->pdo->prepare($this->__toString());
        $query->execute($this->params);
        return $query->rowCount();
    }

    public function __toString()
    {
        return "DELETE FROM `{$this->table}` " . $this->getWhere();
    }


}