<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 13/05/2018
 * Time: 11:31
 */

namespace App\Models;


use Framework\Model;

class Test extends Model
{
    protected $table = "dorian";
    protected $fillable = ["prenom"];
}