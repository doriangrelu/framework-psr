<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 01/05/2018
 * Time: 10:40
 */

namespace App\Framework\Facades;


use Framework\App;
use Framework\Router;

class Route
{
        public static function __callStatic($name, $arguments)
        {
               $router = App::$containerForFacade->get(Router::class);
               return call_user_func_array([$router, $name], $arguments);
        }
}