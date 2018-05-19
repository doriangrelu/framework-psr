<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 29/04/2018
 * Time: 18:40
 */

namespace App\Framework;


use Framework\Renderer;
use Framework\Utility\ControllerUtility;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class Controller
{
    use ControllerUtility;

    /**
     * Controller constructor.
     * @param ServerRequestInterface $request
     * @param ContainerInterface $container
     */
    public function __construct(ServerRequestInterface $request, ContainerInterface $container)
    {
        $this->initialize($request, $container);
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