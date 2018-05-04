<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 08/01/2018
 * Time: 16:20
 */

namespace App\Bundle\Database\Table;


use App\Bundle\Database\Entity\Quotation;
use Framework\Database\Table;

class QuotationTable extends Table
{
    protected $table = "quotations";
    protected $entity = Quotation::class;
}