<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 08/05/2018
 * Time: 10:21
 */

namespace App\Framework\Utility;


use App\Framework\Exception\QueueException;
use phpDocumentor\Reflection\Types\Mixed_;

class Queue
{

    private $array = [];

    private $lastDequeue = 0;

    public function __construct()
    {
    }

    public function enQueue($element)
    {
        $this->array[] = $element;
    }

    /**
     * @return mixed
     * @throws QueueException
     */
    public function deQueue()
    {
        if (count($this->array) > 0 && $this->lastDequeue < count($this->array)) {
            $dequeue = $this->array[$this->lastDequeue];
            $this->lastDequeue++;
            return $dequeue;
        }
        throw new QueueException();
    }

}