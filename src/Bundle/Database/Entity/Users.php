<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 29/11/17
 * Time: 11:17
 */

namespace App\Bundle\Database\Entity;

use Framework\Database\Entity;

class Users extends Entity
{
    public $id;

    public $firstName;

    public $lastName;

    public $mail;

    public $totpKey;

    public $actif;

    public $activeTotp;

    public $idRoles;



}