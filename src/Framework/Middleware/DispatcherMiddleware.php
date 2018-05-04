<?php

namespace Framework\Middleware;

use App\Bundle\Routes;
use Framework\App;
use Framework\Auth\DatabaseAuth;
use Framework\Router;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Config\Definition\Exception\ForbiddenOverwriteException;

class DispatcherMiddleware
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
        $needConnexion = $request->getAttribute("needConnexion");

        if ($needConnexion) {
            $router = $this->container->get(Router::class);
            $uri = $router->generateUri($this->container->get("app.connexion"));
            $response = new Response();
            $response = $response->withHeader("Location", $uri);
            $response = $response->withStatus(301);
            $request = $request->withoutAttribute("needConnexion");
            return $response;
        }
        $route = $request->getAttribute(Router\Route::class);
        if (is_null($route)) {
            return $next($request);
        }

        $callback = $route->getCallback();
        if (is_array($callback)) {
            if (count($callback) == 2 && class_exists($callback[0]) && method_exists($callback[0], $callback[1])) {

                $controller = $callback[0];
                $controllerInstance = new $controller($request, $this->container);
                //$controllerInstance->initialize($request, $this->container);
                $callback = [$controllerInstance, $callback[1]];
                $response = call_user_func_array($callback, $route->getParams());
            } else {
                if (is_callable($callback)) {
                    $response = $callback();
                }
            }
        } else {
            if (is_callable($callback)) {
                $response = $callback();
            }
        }
        $responseBeforeFilter = null;

        if (is_string($response)) {
            return new Response(200, [], $response);
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        } else {
            throw new \Exception('The response is not a string or an instance of ResponseInterface');
        }
    }
}
