<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 15/12/2017
 * Time: 12:51
 */

namespace App\Bundle\Database\Table;


use App\Bundle\Database\Entity\Individual;
use Framework\Database\Table;

class IndividualTable extends Table
{
    protected $table="individual";
    protected $entity=Individual::class;
}