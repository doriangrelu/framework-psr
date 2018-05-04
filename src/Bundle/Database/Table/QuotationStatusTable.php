<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 17/01/2018
 * Time: 11:12
 */

namespace App\Bundle\Database\Table;


use App\Bundle\Database\Entity\QuotationsStatus;
use Framework\Database\Table;

class QuotationStatusTable extends Table
{
    protected $table="quotation_status";
    protected $entity=QuotationsStatus::class;
}