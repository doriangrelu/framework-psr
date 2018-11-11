<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 26/11/2017
 * Time: 16:39
 */

namespace Framework\Auth;


interface UserInterface
{
    /**
     * @return int
     */
    public function getUserId():int;

    /**
     * @return string
     */
    public function getUsername():string;

    /**
     * @return []
     */
    public function getRoles():array;

    public function serialized():string;

    public function unserialized(string $serialisedUser);

}