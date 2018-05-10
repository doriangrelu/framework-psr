<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 03/05/2018
 * Time: 23:36
 */

namespace App\Framework\ORM;


class Entity
{

    private $fields = [];

    public function __get($name)
    {
        return $this->fields[$name] ?? null;
    }

    public function __set($name, $value)
    {
        $this->fields[$name] = $value;
    }



}