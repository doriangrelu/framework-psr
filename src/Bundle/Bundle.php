<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 22/11/17
 * Time: 14:24
 */

namespace App\Bundle;

use App\Bundle\Auth\Model\Connexion;
use Framework\Router;
use Framework\Router\RouterInterface;
use Framework\Utility\ControllerUtility;
use Framework\Utility\MailerUtility;
use Framework\Utility\RequestUtility;
use Framework\Utility\Utility;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Bundle
{
    use Utility;
    use ControllerUtility;
    use RequestUtility;
    use MailerUtility;

    public function __construct(ServerRequestInterface $request, ContainerInterface $container)
    {

        $this->appPage = "Accueil";
    }


    /**
     * Initialise un controlleur en valorisant les propriétés utilities
     * @param ServerRequestInterface $request
     * @param ContainerInterface $container
     */
    public function initialize(ServerRequestInterface $request, ContainerInterface $container)
    {
        $this->setProperties($container);
        $this->setRequestProperties($request);
        $this->setBundleProperties($request, $container);
        $connexionModel=$container->get(Connexion::class);
        $this->renderer->make([
            "connected"=>$connexionModel->userIsConnected()==null?false:true
        ]);
    }

    public static function routes(Router $router):void
    {
        return;
    }

}