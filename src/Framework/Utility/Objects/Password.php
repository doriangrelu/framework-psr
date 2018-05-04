<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 30/11/17
 * Time: 16:11
 */

namespace Framework\Utility\Objects;


use Psr\Container\ContainerInterface;

class Password
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $password
     * @param string $expected
     * @return bool
     */
    public function verify(string $password, string $expected): bool
    {
        $password=$this->insertSalt($password);
        return password_verify($password, $expected);
    }

    /**
     * @param string $string
     * @return string
     */
    public function hash(string $string): string
    {
        return password_hash($this->insertSalt($string), PASSWORD_DEFAULT);
    }

    /**
     * @param string $string
     * @return string
     * @throws \Exception Si le security salt n'est pas définie
     */
    private function insertSalt(string $string): string
    {
        if ($this->container->has("security.salt")) {
            return $string . $this->container->get("security.salt");
        }
        throw new \Exception("Missing security salt in config.php");
    }

}