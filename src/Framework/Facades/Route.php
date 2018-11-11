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

/**
 * Class Route
 * @package App\Framework\Facades
 * @method static Router  scope(string $path, callable $callback)
 * @method static Router\Route  add($path, $action, $name, $method)
 * @method static Router\Route  get(string $path, $action, string $name)
 * @method static Router\Route  put(string $path, $action, string $name)
 * @method static Router\Route  delete(string $path, $action, string $name)
 * @method static Router\Route  update(string $path, $action, string $name)
 * @method static Router\Route  post(string $path, $action, string $name)
 *
 */
class Route
{
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $router = App::$containerForFacade->get(Router::class);
        return call_user_func_array([$router, $name], $arguments);
    }
}