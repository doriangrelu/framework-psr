<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 02/12/2018
 * Time: 13:55
 */

namespace Framework\Provider;


use App\Event\ErrorHandler;
use App\Framework\Exception\ProviderException;
use DI\ContainerBuilder;
use Doctrine\ORM\Tools\Setup;
use PDO;
use Framework\Middleware\CsrfMiddleware;
use Framework\Middleware\DispatcherMiddleware;
use Framework\Middleware\EventsMiddleware;
use Framework\Middleware\HttpMethodMiddleware;
use Framework\Middleware\MethodMiddleware;
use Framework\Middleware\NotFoundMiddleware;
use Framework\Middleware\RouterMiddleware;
use Framework\Middleware\TrailingSlashMiddleware;
use Framework\Mode;
use Framework\Twig\ActiveExtension;
use Framework\Twig\CsrfExtension;
use Framework\Twig\CssExtension;
use Framework\Twig\FlashExtension;
use Framework\Twig\FormExtension;
use Framework\Twig\JsExtension;
use Framework\Twig\PagerFantaExtension;
use Framework\Twig\RouterTwigExtension;
use Framework\Twig\TextExtension;
use Framework\Twig\TimeExtension;
use Middlewares\Whoops;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

/**
 * Class Services
 * @package Framework
 */
abstract class AbstractProvider
{
    private $_middlewaresLoaded = false;

    private $_eventsLoader = false;

    private $_middlewares = [];

    private $_definitionsContainer = [
        'twig.extensions' => [],
    ];

    private $_events = [];

    /**
     * @var ContainerInterface
     */
    private $_container = null;

    public function __construct(array $configurationFile)
    {
        foreach ($configurationFile as $configuration) {
            $this->_definitionsContainer = array_merge($this->_definitionsContainer, $configuration);
        }
    }

    private function _baseMiddlewares(): void
    {
        $this->_middlewares = array_merge($this->_middlewares, [
            TrailingSlashMiddleware::class,
            HttpMethodMiddleware::class,
            EventsMiddleware::class,
            MethodMiddleware::class,
            CsrfMiddleware::class,
            RouterMiddleware::class,
            DispatcherMiddleware::class,
            NotFoundMiddleware::class,
            Whoops::class
        ]);
    }

    private function _baseTwigDefinitions(): void
    {
        $this->_definitionsContainer['twig.extensions'] = array_merge($this->_definitionsContainer['twig.extensions'], [
            \DI\get(RouterTwigExtension::class),
            \DI\get(PagerFantaExtension::class),
            \DI\get(TextExtension::class),
            \DI\get(TimeExtension::class),
            \DI\get(FlashExtension::class),
            \DI\get(FormExtension::class),
            \DI\get(CsrfExtension::class),
            \DI\get(ActiveExtension::class),
            \DI\get(CssExtension::class),
            \DI\get(JsExtension::class)
        ]);
    }

    private function _baseEventDefinitions(): void
    {
        $this->_addEventProvider(ErrorHandler::class);
    }

    /**
     * @throws ProviderException
     */
    private function _baseDefinitions(): void
    {

        $this->_addDefinition(PDO::class, function (ContainerInterface $c) {
            return new PDO(
                'mysql:host=' . $c->get('database.host') . ';dbname=' . $c->get('database.name'),
                $c->get('database.username'),
                $c->get('database.password'),
                [
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]
            );
        });
        $this->_addDefinition(EntityManager::class, function (ContainerInterface $c) {
            $dbParams = array(
                'driver' => 'pdo_mysql',
                'user' => $c->get("database.username"),
                'password' => $c->get("database.password"),
                'dbname' => $c->get("database.name"),
                'host' => $c->get("database.host")
            );
            $paths = [dirname(__DIR__) . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "Database" . DIRECTORY_SEPARATOR . "Entity"];
            $isDevMode = $c->get("mode") === Mode::DEVELOPPEMENT;
            $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, null, false);
            return EntityManager::create($dbParams, $config);;
        });
    }

    protected function _addEventProvider($event): AbstractProvider
    {
        $this->_events[] = $event;
        return $this;
    }

    /**
     * @return array
     */
    public function getEvents(): array
    {
        if ($this->_eventsLoader === false) {
            $this->_baseEventDefinitions();
        }
        return $this->_events;
    }

    /**
     * @param string $alias
     * @param $definition
     * @return AbstractProvider
     * @throws ProviderException
     */
    protected function _addDefinition(string $alias, $definition): AbstractProvider
    {
        if (isset($this->_definitionsContainer[$alias])) {
            throw new ProviderException("Definition with alias $alias already exists");
        }
        $this->_definitionsContainer[$alias] = $definition;
        return $this;
    }

    /**
     * @param $middleware
     * @return AbstractProvider
     */
    protected function _addMiddleware($middleware): AbstractProvider
    {
        $this->_middlewares[] = $middleware;
        return $this;
    }

    protected function _addTwigDefinition($definition): AbstractProvider
    {
        $this->_definitionsContainer['twig.extensions'][] = $definition;
        return $this;
    }

    protected abstract function _setProviders(): void;

    protected abstract function _setAuthProviders(): void;

    protected abstract function _setMiddlewaresProviders(): void;

    protected abstract function _setTwigProviders(): void;

    protected abstract function _setEventProviders(): void;

    /**
     * @return array
     */
    public function getMiddlewares(): array
    {
        if ($this->_middlewaresLoaded === false) {
            $this->_baseMiddlewares();
            $this->_middlewaresLoaded = true;
        }
        return $this->_middlewares;
    }

    /**
     * @return ContainerInterface
     * @throws \Exception
     */
    public function getContainer(): ?ContainerInterface
    {
        if ($this->_container === null) {
            $this->_baseDefinitions();
            $this->_setProviders();
            $this->_setAuthProviders();
            $this->_baseTwigDefinitions();
            $this->_definitionsContainer = array_merge($this->_definitionsContainer, ['events'=>$this->getEvents()]);
            $builder = new ContainerBuilder();
            foreach ([$this->_definitionsContainer] as $definition) {


                $builder->addDefinitions($definition);
            }
            $this->_container = $builder->build();
        }
        return $this->_container;
    }

}