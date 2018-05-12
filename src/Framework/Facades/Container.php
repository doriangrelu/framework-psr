<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 10/05/2018
 * Time: 19:12
 */

namespace App\Framework\Facades;


use App\Framework\Exception\ContainerFacadeException;
use Framework\App;

class Container
{
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws ContainerFacadeException
     */
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([App::$containerForFacade, $name], $arguments);
    }
}