<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 29/11/17
 * Time: 16:59
 */

namespace App\Bundle\Auth;


use App\Bundle\Auth\Controller\Auth;
use App\Bundle\Auth\Controller\Totp;
use App\Bundle\Bundle;
use Framework\Auth\AuthInterface;
use Framework\Auth\DatabaseAuth;
use Framework\Router;
use Framework\Router\RouteScope;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthBundle extends Bundle
{
    /**
     * @var AuthInterface
     */
    protected $databaseAuth;

    public function initialize(ServerRequestInterface $request, ContainerInterface $container)
    {
        parent::initialize($request, $container); // TODO: Change the autogenerated stub
        $this->databaseAuth = $container->get(DatabaseAuth::class);
        $this->renderer->setLayout("Auth");
    }

    public static function routes(Router $router): void
    {

        $router->scope("/authentification/", function (RouteScope $routes) {
            $routes->get("connexion", [Auth::class, "formulaireConnexion"], "form.connexion");
            $routes->get("deconnexion", [Auth::class, "formulaireConnexion"], "form.deconnexion");
            $routes->get("verification", [Totp::class, "showCode"], "form.totp");
            $routes->post("verification", [Totp::class, "showCode"], "form.totp.send");

            $routes->get("inscription", [Auth::class, "formulaireInscription"], "form.inscription");
            $routes->post("connexion", [Auth::class, "seConnecter"], "connexion");
            $routes->post("inscription", [Auth::class, "setInscription"], "inscription");

        });

    }
}