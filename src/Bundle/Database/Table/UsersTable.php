<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 29/11/17
 * Time: 11:19
 */

namespace App\Bundle\Database\Table;


use App\Bundle\Database\Entity\Users;
use Framework\Database\Table;

class UsersTable extends Table
{
    protected $entity = Users::class;
    protected $table = "users";

}