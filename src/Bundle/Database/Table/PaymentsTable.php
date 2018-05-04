<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 08/01/2018
 * Time: 16:39
 */

namespace App\Bundle\Database\Table;


use App\Bundle\Database\Entity\Payments;
use Framework\Database\Table;

class PaymentsTable extends Table
{
    protected $table = "payments";
    protected $entity = Payments::class;
}