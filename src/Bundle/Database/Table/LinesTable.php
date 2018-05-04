<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 08/01/2018
 * Time: 16:21
 */

namespace App\Bundle\Database\Table;


use App\Bundle\Database\Entity\Lines;
use Framework\Database\Table;

class LinesTable extends Table
{
    protected $table = "lines";
    protected $entity = Lines::class;
}