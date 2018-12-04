<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 04/12/2018
 * Time: 15:51
 */

namespace Framework\Interfaces;


interface Serializable
{
    /**
     * @return string
     */
    public function serialize(): string;
}