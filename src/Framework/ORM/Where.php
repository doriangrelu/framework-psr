<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 03/05/2018
 * Time: 23:21
 */

namespace App\Framework\ORM;


use Pekkis\Queue\Queue;

class Where
{


    private $queue = [];


    public function field(string $field):self{

        $this->queue =  new Queue();
        return $this;
    }

    public function equals($value):self{

        return $this;
    }

    public function superiorThan($value):self{

        return $this;
    }

    public function lessThan($value):self{

        return $this;
    }

    public function and():self{

        return $this;
    }

    public function or():self{

        return $this;
    }


}