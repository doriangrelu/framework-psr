<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 27/12/2017
 * Time: 20:54
 */

namespace Framework\Database;


class Entity
{

    public $createdAt=null;

    public $updatedAt=null;

    public function setCreatedAt($datetime)
    {
        if (is_string($datetime)) {
            $this->createdAt = new \DateTime($datetime);
        }
    }

    public function setUpdatedAt($datetime)
    {
        if (is_string($datetime)) {
            $this->updatedAt = new \DateTime($datetime);
        }
    }
}