<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 29/12/2017
 * Time: 11:02
 */

namespace Framework\Cookie;

use Framework\Session\SessionInterface;
use Psr\Container\ContainerInterface;

class PHPCookie implements CookieInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * PHPCookie constructor.
     * @param ContainerInterface $container
     * @param SessionInterface $session
     */
    public function __construct(ContainerInterface $container, SessionInterface $session)
    {

        $this->container = $container;
    }

    /**
     * @param string $name
     * @param string $value
     * @param int $expire
     * @return bool
     */
    public function set(string $name, $value, int $expire=50): bool
    {
        $expire=365*24*3600;
        return setcookie($name, $value,(time()+$expire));
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function get(string $name):?mixed
    {
        if($this->exist($name)){
            return $_COOKIE[$name];
        }
        return null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function exist(string $name): bool
    {
        return isset($_COOKIE[$name]);
    }

    /**
     * @param string $name
     */
    public function delete(string $name): void
    {
        setCookie($name, '', (time() - 3600));
    }
}