<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 17/05/2018
 * Time: 18:32
 */

namespace App\Routes\Roles;


use App\Framework\Auth\Role;

class Member implements Role
{

    private $idRole;
    private $name;

    /**
     * AdminRole constructor.
     * @param $idRole
     * @param $name
     */
    public function __construct($idRole, $name)
    {
        $this->idRole = $idRole;
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getIdRole():int
    {
        return $this->idRole;
    }

    /**
     * @return string
     */
    public function getName():string
    {
        return $this->name;
    }




}