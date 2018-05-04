<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 29/04/2018
 * Time: 18:40
 */

namespace App\Framework;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class Controller
{

    /**
     * @var ServerRequestInterface
     */
    protected $request;
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Controller constructor.
     * @param ServerRequestInterface $request
     * @param ContainerInterface $container
     */
    public function __construct(ServerRequestInterface $request, ContainerInterface $container)
    {
        $this->request = $request;
        $this->container = $container;
    }

    /**
     * Policy Controller
     * @return array
     */
    public function policies(): array
    {
        return [

        ];
    }


}