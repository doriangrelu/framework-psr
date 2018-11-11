<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 30/11/17
 * Time: 16:51
 */

namespace Framework\Utility;

use App\Framework\Auth\AuthInterface;
use Doctrine\ORM\EntityManager;
use Framework\Middleware\CsrfMiddleware;
use Framework\Renderer;
use Framework\Router;
use Framework\Router\RouterInterface;
use Framework\Session\FlashService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

trait ControllerUtility
{
    /**
     * @var AuthInterface
     */
    private $_auth;

    /**
     * Représente la page active
     * @var
     */
    protected $appPage;


    /**
     * @var Renderer
     */
    private $_renderer;

    /**
     * @var ServerRequestInterface
     */
    private $_request;

    /**
     * @var ContainerInterface
     */
    private $_container;

    /**
     * @var Router
     */
    private $_router;

    /**
     * @var array
     */
    protected $errorsValue;

    /**
     * @var array
     */
    private $activeTable;

    /**
     * @param ContainerInterface $container
     */
    public function initialize(ContainerInterface $container)
    {
        $this->_container = $container;
        $this->_container->get(Renderer::class)->make([
            "appName" => $this->_container->get("name")
        ]);
        $this->activeTable = [];
    }

    /**
     * @param string $controllerAction
     * @return bool
     */
    public function getSecurity(string $controllerAction): bool
    {
        return $this->getAuth()->access($controllerAction);
    }

    /**
     * @return Renderer
     */
    protected function getRenderer(): Renderer
    {
        return $this->_renderer;
    }

    /**
     * Define rules Auth Component Here
     */
    public function setSecurity(): void
    {

    }

    protected function getRequest(): ServerRequestInterface
    {
        return $this->_container->get(ServerRequestInterface::class);
    }

    /**
     * @return string
     */
    protected function generateTokenCsrf(): string
    {
        $csrfMiddleware = $this->_container->get(CsrfMiddleware::class);
        return $csrfMiddleware->generateToken();
    }

    /**
     * @return AuthInterface
     */
    protected function getAuth(): AuthInterface
    {
        return $this->_container->get(AuthInterface::class);
    }

    /**
     * @param string $pageName
     */
    protected function setPage(string $pageName): void
    {
        $this->_container->get(Renderer::class)->make([
            "appPage" => $pageName
        ]);
    }

    /**
     * @param string $name
     * @param array|null $params
     * @param array|null $queryParams
     * @return null|string
     */
    protected function generateUri(string $name, ?array $params = [], ?array $queryParams = []): ?string
    {
        return $this->_container->get(RouterInterface::class)->generateUri($name, $params, $queryParams);
    }

    /**
     * @param string $name
     * @param array|null $params
     * @param array|null $queryParams
     * @return ResponseInterface
     */
    protected function redirect(string $name, ?array $params = [], ?array $queryParams = []): ResponseInterface
    {
        return $this->_container->get(RouterInterface::class)->redirect($name, $params, $queryParams);
    }

    /**
     * @return EntityManager
     */
    protected function table(): EntityManager
    {
        return $this->_container->get(EntityManager::class);
    }

    /**
     * @return FlashService
     */
    protected function getFlash(): FlashService
    {
        return $this->_container->get(FlashService::class);
    }

}