<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 17/05/2018
 * Time: 18:28
 */

namespace App\Framework\Auth;


interface Role
{
    public function getIdRole():int;
    public function getName():string;
}