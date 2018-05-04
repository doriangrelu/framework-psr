<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 18/01/2018
 * Time: 11:20
 */

namespace Framework\Types;


interface Comparable
{
    /**
     * @param Comparable $object
     * @return int
     */
    public function compareTo(Comparable $object): int;
}