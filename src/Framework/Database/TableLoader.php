<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 29/11/17
 * Time: 11:26
 */

namespace Framework\Database;

use Psr\Container\ContainerInterface;

/**
 * Class permettant de charger simplement un objet table
 * Class TableLoader
 * @package Framework\Database
 */
class TableLoader
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * TableLoader constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container=$container;
    }

    /**
     * @param string $class
     * @return Table
     */
    public function load(string $class):Table
    {
        return $this->container->get($class);
    }
}