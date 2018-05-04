<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 16/01/2018
 * Time: 17:13
 */

namespace App\Bundle\Database\Table;


use App\Bundle\Database\Entity\PaymentsType;
use Framework\Database\Table;

class PaymentsTypeTable extends Table
{
    protected $table="payments_type";
    protected $entity=PaymentsType::class;
}