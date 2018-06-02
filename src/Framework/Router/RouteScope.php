<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 14/11/17
 * Time: 14:45
 */

namespace Framework\Router;


use App\Framework\Auth\Role;
use Framework\Router;

class RouteScope extends RouterFactory
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var array
     */
    private $middlewares = [];

    /**
     * @var Role[]
     */
    private $roles = [];

    /**
     * RouteScope constructor.
     * @param string $prefix
     * @param Router $router
     * @param array $middlewares
     * @param Role[] $roles
     */
    public function __construct(string $prefix, Router $router, array $middlewares =[], array $roles = [])
    {
        $this->prefix=trim($prefix, "/");
        $this->middlewares = $middlewares;
        $this->router=$router;
        $this->roles=$roles;
    }

    /**
     * @param $path
     * @param $action
     * @param $name
     * @param $method
     * @return Route
     * @throws \Exception
     */
    public function add($path, $action, $name, $method): Route
    {
        $path=$this->prefix."/".trim($path, "/");
        $route = $this->router->add($path, $action, $name, $method);
        foreach($this->middlewares as $middleware){
            $route->bind($middleware);
        }
        foreach ($this->roles as $role){
            $route->require($role);
        }
        return $route;
    }

    /**
     * @param string $path
     * @param callable $callback
     * @param array $middlewares
     * @param Role[] $roles
     */
    public function scope(string $path, callable $callback, array $middlewares=[], array $roles = []): void
    {
        $path=$this->prefix."/".trim($path, "/");
        $this->middlewares = $middlewares;
        $scoper= new RouteScope($path, $this->router, $middlewares);
        $callback($scoper);
    }
}