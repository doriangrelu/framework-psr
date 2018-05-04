<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 04/01/2018
 * Time: 18:20
 */

namespace App\Bundle\Database\Table;


use App\Bundle\Database\Entity\Unity;
use Framework\Database\Table;

class UnityTable extends Table
{
    protected $entity=Unity::class;
    protected $table="unity_type";
}