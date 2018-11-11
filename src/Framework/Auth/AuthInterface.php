<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 11/11/2018
 * Time: 15:58
 */

namespace App\Framework\Auth;


use Framework\Auth\UserInterface;

interface AuthInterface
{

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface;

    /**
     * @return Auth
     */
    public function allowController(): AuthInterface;

    /**
     * @param $allowedMethod
     * @return Auth
     */
    public function allowMethods($allowedMethod): AuthInterface;

    /**
     * @param string $requestedMethod
     * @return bool
     */
    public function access(string $requestedMethod): bool;


}