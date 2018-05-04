<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 26/11/2017
 * Time: 17:06
 */

namespace Framework\Router;


class PolicyRoute
{
    /**
     * @var null|mixed
     */
    private $bundle;
    /**
     * @var array
     */
    private $roles=[];
    /**
     * @var null|string
     */
    private $routeName;
    /**
     * @var null|mixed
     */
    private $class;
    /**
     * @var null|string
     */
    private $method;


    /**
     * PolicyRoute constructor.
     * @param array $roles
     * @internal param mixed $class
     * @internal param null|string $method
     * @internal param mixed|null $bundle
     * @internal param null|string $routeName
     */
    public function __construct(array $roles)
    {
        $this->roles = $roles;
        $this->routeName = null;
        $this->class = "*";
        $this->method = "*";
        $this->bundle=null;
    }

    /**
     * @param mixed $bundle
     * @return PolicyRoute
     */
    public function setBundle($bundle):self
    {
        $this->bundle = $bundle;
        return $this;
    }

    /**
     * @param null|string $routeName
     * @return PolicyRoute
     */
    public function setRouteName($routeName):self
    {
        $this->routeName = $routeName;
        return $this;
    }

    /**
     * @param mixed $class
     * @return PolicyRoute
     */
    public function setClass($class):self
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @param null|string $method
     * @return PolicyRoute
     */
    public function setMethod($method):self
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @return null|string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return null|string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return mixed|null
     */
    public function getBundle()
    {
        return $this->bundle;
    }

}