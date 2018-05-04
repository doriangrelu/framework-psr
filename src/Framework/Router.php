<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 29/11/17
 * Time: 12:23
 */

namespace Framework;

use Framework\Router\Method;
use Framework\Router\Route;
use Framework\Router\RouterAware;
use Framework\Router\RouterFactory;
use Framework\Router\RouterInterface;
use Framework\Router\RouteScope;
use Psr\Http\Message\ServerRequestInterface;

class Router extends RouterFactory implements RouterInterface
{
    use RouterAware;
    /**
     * @var Route[]
     */
    private $routes=[];

    /**
     * @param string $path
     * @param callable $callback
     * @param array $middlewares
     */
    public function scope(string $path, callable $callback, array $middlewares = []): void
    {
        $scoper=new RouteScope($path, $this, $middlewares);
        $callback($scoper);
    }

    public function getRoutes():array
    {
        return $this->routes;
    }

    /**
     * @param $path
     * @param $action
     * @param $name
     * @param $method
     * @return Route
     * @throws \Exception
     */
    public function add($path, $action, $name, $method): Route
    {
        if (!$this->routeNameAsAlreadyDefined($name)) {
            $route = new Route($path, $action, $name, $method);
            $this->routes[] = $route;
            return $route;
        }
        throw new \Exception("Une route <$name> est déjà définie");
    }

    /**
     * @param string $name
     * @param array $params
     * @param array $queryParams
     * @return null|string
     */
    public function generateUri(string $name, array $params = [], array $queryParams = []): ?string
    {
        $uri = null;
        foreach ($this->routes as $route) {
            if ($route->getName() == $name) {
                $uri = $route->getUri($params);
            }
        }
        if (!is_null($uri) && !empty($queryParams)) {
            return $uri . '?' . http_build_query($queryParams);
        }
        return $uri;
    }

    /**
     * @param string $name
     * @return bool
     */
    private function routeNameAsAlreadyDefined(string $name): bool
    {
        foreach ($this->routes as $route) {
            if ($route->getName() == $name) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request):?Route
    {
        if(!Mode::is_cli()){
            $uri = str_replace(WEB_ROOT, "", $request->getUri()->getPath());
        } else {
            $uri=$request->getUri()->getPath();
        }
        $uri=(empty($uri)?"/":$uri);
        foreach ($this->routes as $route) {
            if ($route->match($uri) && $route->getMethod() == $request->getMethod()) {
                return $route;
            }
        }
        return null;
    }
}