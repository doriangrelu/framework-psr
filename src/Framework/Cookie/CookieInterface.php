<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 29/12/2017
 * Time: 11:03
 */

namespace Framework\Cookie;


interface CookieInterface
{

    /**
     * @param string $name
     * @param string $value
     * @param int $expire
     * @return bool
     */
    public function set(string $name, $value, int $expire=50):bool;

    /**
     * @param string $name
     * @return mixed|null
     */
    public function get(string $name);

    /**
     * @param string $name
     * @return bool
     */
    public function exist(string $name):bool;

    /**
     * @param string $name
     */
    public function delete(string $name):void;

}