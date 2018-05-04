<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 26/11/2017
 * Time: 16:58
 */

namespace Framework\Middleware;


use App\Bundle\Bundle;
use App\Bundle\Routes;
use Framework\Auth\DatabaseAuth;
use Framework\Router\Route;
use Framework\Router\RouterInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Config\Definition\Exception\ForbiddenOverwriteException;

class LoggedInMiddleware
{
    /**
     * @var ContainerInterface
     */
    private $container;


    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $route = $request->getAttribute(Route::class);
        //$request=$request->withAttribute("needConnexion", false);
        /*if (!is_null($route)) {

            $auth=$this->container->get(DatabaseAuth::class);

            $callback = $route->getCallBack();
            if(is_array($callback)){
                $controller = (isset($callback[0]) ? $callback[0] : null);
                $method = (isset($callback[1]) ? $callback[1] : null);
                $routeName = $route->getName();
                //Policies
                $bundle = Bundle::getBundleName(Bundle::getBundleNameFromController($controller));
                $policy = Routes::getPolicy($bundle, $controller, $method, $routeName);

                if (!is_null($policy) && count($policy->getRoles()) > 0) {
                    if (is_null($auth->getUser()) || !array_in_array($auth->getUser()->getRoles(), $policy->getRoles())) { //Un utilistaeur connecté, vérification des roles
                        //Renvois forbbiden
                        $router=$this->container->get(RouterInterface::class);
                        $uri=$router->generateUri($this->container->get("app.connexion"));
                        $request=$request->withAttribute("needConnexion", true);
                    } //sinon utilisateur connecté, on passe au Middleware suivant
                }
            }
        }*/
        return $next($request);
    }
}