<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 15/12/2017
 * Time: 12:59
 */

namespace App\Bundle\Database\Table;

use App\Bundle\Database\Entity\Professionnal;
use Framework\Database\Table;

class ProfessionnalTable extends Table
{
    protected $table="professionnal";
    protected $entity=Professionnal::class;
}