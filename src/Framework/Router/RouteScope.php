<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 14/11/17
 * Time: 14:45
 */

namespace Framework\Router;


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
     * RouteScope constructor.
     * @param string $prefix
     * @param Router $router
     * @param array $middlewares
     */
    public function __construct(string $prefix, Router $router, array $middlewares =[])
    {
        $this->prefix=trim($prefix, "/");
        $this->middlewares = $middlewares;
        $this->router=$router;
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
        return $route;
    }

    /**
     * @param string $path
     * @param callable $callback
     * @param array $middlewares
     */
    public function scope(string $path, callable $callback, array $middlewares=[]): void
    {
        $path=$this->prefix."/".trim($path, "/");
        $this->middlewares = $middlewares;
        $scoper= new RouteScope($path, $this->router, $middlewares);
        $callback($scoper);
    }
}