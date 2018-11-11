<?php

namespace Framework;

use App\Framework\Event\Emitter;
use App\Framework\Event\SubScriberInterface;
use DI\ContainerBuilder;
use Doctrine\Common\Cache\FilesystemCache;
use Framework\Middleware\LoggedInMiddleware;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App implements DelegateInterface
{

    /**
     * @var array
     */
    private $definition = [];

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var
     */
    public static $containerForFacade;

    /**
     * @var string[]
     */
    private $middlewares;

    /**
     * @var int
     */
    private $index = 0;

    /**
     * @var SubScriberInterface[]
     */
    private $subScribers = [];

    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * App constructor.
     * @throws \App\Framework\Event\DoubleEventException
     */
    public function __construct()
    {
        $definitions = require "config/app.php";
        $arrayDefinitions = require 'config/services.php';

        if (is_array($definitions)) {
            foreach ($definitions as $type => $definition) {
                switch ($type) {
                    case "middlewares":
                        $this->middlewares = $definition;
                        break;
                    case "twig.extensions":
                        $arrayDefinitions[$type] = $definition;
                        break;
                    case "subscribers":
                        $this->subScribers = array_merge($this->subScribers, $definition);
                        break;
                    default:
                        $arrayDefinitions = array_merge($arrayDefinitions, $definition);
                        break;
                }
            }
            $this->definition[] = $arrayDefinitions;
            self::$containerForFacade = $this->getContainer();
            $this->addHandler();
        }
    }


    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if (is_null($middleware)) {
            throw new \Exception('Aucun middleware n\'a intercepté cette requête');
        } elseif (is_callable($middleware)) {
            return call_user_func_array($middleware, [$request, [$this, 'process']]);
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
    }


    private function bindRouteMiddleware(ServerRequestInterface $request)
    {
        $route = $this->getContainer()->get(Router::class)->match($this->request);
        if (!is_null($route)) {
            $middlewaresRoute = $route->getMiddlewares();
            $binding = [];
            $before = null;
            foreach ($this->middlewares as $middleware) {
                if ($before === LoggedInMiddleware::class) {
                    foreach ($middlewaresRoute as $middlewareRoute) {
                        $binding[] = $middlewareRoute;
                    }
                }
                $binding[] = $middleware;
                $before = $middleware;
            }
            $this->middlewares = $binding;
        }
    }

    /**
     * Container Interface
     * @return ContainerInterface
     */
    public static function container(): ?ContainerInterface
    {
        return self::$containerForFacade;
    }

    /**
     * @throws \App\Framework\Event\DoubleEventException
     */
    private function addHandler()
    {
        foreach ($this->subScribers as $subscriber) {
            $this->getContainer()->get(Emitter::class)->addSubScriber($this->getContainer()->get($subscriber));
        }
    }


    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;
        self::$containerForFacade = $this->getContainer();
        require "src/Routes/web.php";
        $this->bindRouteMiddleware($request);
        $request = $request->withAttribute("container", $this->getContainer());
        return $this->process($request);
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new ContainerBuilder();
            $env = getenv('ENV') ?: 'dev';
            if ($env === 'production') {
                $builder->setDefinitionCache(new FilesystemCache('tmp/di'));
                $builder->writeProxiesToFile(true, 'tmp/proxies');
            }
            foreach ($this->definition as $definition) {
                $builder->addDefinitions($definition);
            }

            $this->container = $builder->build();
        }

        return $this->container;
    }

    /**
     * @return object
     */
    private function getMiddleware()
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            $middleware = $this->container->get($this->middlewares[$this->index]);
            $this->index++;
            return $middleware;
        }
        return null;
    }


}
