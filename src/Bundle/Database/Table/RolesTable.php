<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 29/11/17
 * Time: 18:04
 */

namespace App\Bundle\Database\Table;


use App\Bundle\Database\Entity\Roles;
use Framework\Database\Table;

class RolesTable extends Table
{
    protected $entity = Roles::class;
    protected $table = "roles";
}