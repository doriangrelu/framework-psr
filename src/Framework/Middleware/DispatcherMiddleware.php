<?php

namespace Framework\Middleware;

use App\Framework\Controller;
use App\Framework\Exception\Http\ForbiddenHttpException;
use App\Framework\Exception\Router\MissingRouteException;
use Framework\Router;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


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

    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return Response|mixed
     * @throws ForbiddenHttpException
     * @throws MissingRouteException
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $route = $request->getAttribute(Router\Route::class);
        if (is_null($route)) {
            return $next($request);
        }

        $callback = $route->getCallback();
        if (is_array($callback)) {
            if (count($callback) == 2 && class_exists($callback[0]) && method_exists($callback[0], $callback[1])) {
                $this->container->set(ServerRequestInterface::class, $request);
                $controller = $this->container->get($callback[0]);
                $controller->initialize($this->container);
                $controller->setSecurity();
                if ($this->container->get('Auth.actived')) {
                    $result = $this->_applyAuth($controller, $callback[1]);
                    if (!is_null($result)) {
                        return $result;
                    }
                }

                $callback = [$controller, $callback[1]];
                $response = call_user_func_array($callback, $route->getParams());
            }
        }
        if (is_callable($callback)) {
            $response = $callback();
        }


        if (is_string($response)) {
            return new Response(200, [], $response);
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        } else {
            throw new \Exception('The response is not a string or an instance of ResponseInterface');
        }
    }

    /**
     * @param $controller
     * @param $method
     * @return \GuzzleHttp\Psr7\MessageTrait|Response|null
     * @throws ForbiddenHttpException
     * @throws MissingRouteException
     */
    private function _applyAuth(Controller $controller, string $method): ?Response
    {
        if (!$controller->getSecurity($method)) {
            if ($this->container->get('Auth.Exception')) {
                throw new ForbiddenHttpException();
            }
            $response = new Response();
            $router = $this->container->get(Router\RouterInterface::class);
            $response = $response->withStatus(301);
            $url = $router->generateUri($this->container->get('Auth.route.redirectLogin'));
            if (is_null($url)) {
                throw new MissingRouteException("Missing route with name: {$this->container->get('Auth.route.redirectLogin')}");
            }
            $response = $response->withHeader('Location', $url);
            return $response;
        }
        return null;
    }

}
