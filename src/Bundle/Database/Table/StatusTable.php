<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 08/01/2018
 * Time: 16:18
 */

namespace App\Bundle\Database\Table;


use App\Bundle\Database\Entity\Status;
use Framework\Database\Table;

class StatusTable extends Table
{
    const EN_COURS_DE_SAISIE="en-cours-de-saisie";
    const SAISIE_CLOTURE="saisie-cloture";

    protected $table="status";
    protected $entity=Status::class;

}