<?php

namespace Framework;

use App\Bundle\Bundle;

use App\Bundle\Routes;

use App\Framework\Dorian;
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
     * List of modules
     * @var Bundle[]
     */
    private $modules = [];
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
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * App constructor.
     * @param $definition
     */
    public function __construct()
    {
        $definitions = require "config/app.php";
        $arrayDefinitions = [];
        if (is_array($definitions)) {
            foreach ($definitions as $type => $definition) {
                switch ($type) {
                    case "middlewares":
                        $this->middlewares = $definition;
                        break;
                    case "twig.extensions":
                        $arrayDefinitions[$type] = $definition;
                        break;
                    default:
                        $arrayDefinitions = array_merge($arrayDefinitions, $definition);
                        break;
                }
            }

            $this->definition[] = $arrayDefinitions;

        }
    }

    /**
     * Rajoute un module à l'application
     *
     * @param string $module
     * @return App
     */
    public function addModule(string $module): self
    {
        $this->modules[] = $module;
        return $this;
    }

    /**
     * Ajoute un middleware
     *
     * @param string $middleware
     * @return App
     */
    public function pipe(string $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
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


    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;
        self::$containerForFacade = $this->getContainer();
        require "src/routes/web.php";
        $this->bindRouteMiddleware($request);
        $request = $request->withAttribute("container", $this->getContainer());

        $model = $this->getContainer()->get(Dorian::class);

        $model->prenom="Dorian";

        dd($model->select()->fetchAll()->get(0)->select()->fetchAll()->prenom="dorian");

        die();

        return $this->process($request);

    }


    private function initRoutesModules(): void
    {
        Routes::init($this->getContainer());
        foreach ($this->modules as $module) {
            $module::routes($this->container->get(Router::class));
        }
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

    /**
     * @return array
     */
    public function getModules(): array
    {
        return $this->modules;
    }

}
