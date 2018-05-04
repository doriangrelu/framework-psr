<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 15/12/2017
 * Time: 12:52
 */

namespace App\Bundle\Database\Entity;


class Individual
{
    public $createdAt;

    public $updatedAt;

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