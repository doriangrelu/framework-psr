<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 03/05/2018
 * Time: 23:18
 */

namespace App\Framework\ORM;


abstract class Query
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * Query constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }



}