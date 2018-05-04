<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 01/05/2018
 * Time: 17:55
 */

namespace App\Framework\Event;


interface SubScriberInterface
{
    /**
     * @return array
     */
    public function getEvents():array;
}