<?php

namespace Framework;

use App\Framework\Event\Emitter;
use App\Provider\ProviderManager;
use Framework\Provider\AbstractProvider;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App implements DelegateInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var AbstractProvider
     */
    private $provider;
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
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * App constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $config = require "config/app.php";
        $provider = new ProviderManager($config);
        $this->provider = $provider;
        $this->container = $provider->getContainer();
        $this->middlewares = $provider->getMiddlewares();
        self::$containerForFacade = $provider->getContainer();
        $this->attachEvents();
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
     * @throws \Exception
     */
    private function attachEvents()
    {
        foreach ($this->provider->getEvents() as $subscriber) {
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
        $request = $request->withAttribute("container", $this->getContainer());
        return $this->process($request);
    }

    /**
     * @return ContainerInterface
     * @throws \Exception
     */
    public function getContainer(): ContainerInterface
    {
        return $this->provider->getContainer();
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
