<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 01/05/2018
 * Time: 15:49
 */

namespace App\Framework\Auth;


class Policies
{
    public function denyController(string $controller,array $rankRequired){

    }

    public function denyControllerMethod(string $controller, string $method, array $rankRequired){

    }

    public function denyRoute($routeName, array $rankRequired){

    }

    public function denyScope($scope, array $rankRequired){

    }
}