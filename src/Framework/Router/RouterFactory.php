<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 29/11/17
 * Time: 14:09
 */

namespace Framework\Router;


use Framework\Router;

abstract class RouterFactory
{

    /**
     * @param string $path
     * @param $action
     * @param string $name
     * @return Route
     */
    public function get(string $path, $action, string $name): Route
    {
        return $this->add($path, $action, $name, "GET");
    }

    /**
     * @param string $path
     * @param $action
     * @param string $name
     * @return Route
     */
    public function put(string $path, $action, string $name): Route
    {
        return $this->add($path, $action, $name, "PUT");
    }

    /**
     * @param string $path
     * @param $action
     * @param string $name
     * @return Route
     */
    public function delete(string $path, $action, string $name): Route
    {
        return $this->add($path, $action, $name, "DELETE");
    }

    /**
     * @param string $path
     * @param $action
     * @param string $name
     * @return Route
     */
    public function update(string $path, $action, string $name): Route
    {
        return $this->add($path, $action, $name, "UPDATE");
    }

    /**
     * @param string $path
     * @param $action
     * @param string $name
     * @return Route
     */
    public function post(string $path, $action, string $name): Route
    {
        return $this->add($path, $action, $name, "POST");
    }

    public abstract function add($path, $action, $name, $method);

    /**
     * @param string $path
     * @param callable $callback
     */
    public abstract function scope(string $path, callable $callback):void;
}