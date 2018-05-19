<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 19/05/2018
 * Time: 11:38
 */

namespace App\Models;


use Framework\Model;

class Users extends Model
{
    /**
     * User Id
     * @var mixed
     */
    public $id;
    /**
     * User password
     * @var string
     */
    public $password;
    /**
     * SecurityToken
     * @var string
     */
    public $token;
}