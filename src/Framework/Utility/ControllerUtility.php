<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 30/11/17
 * Time: 16:51
 */

namespace Framework\Utility;

use Doctrine\ORM\EntityManager;
use Framework\Cookie\CookieInterface;
use Framework\Middleware\CsrfMiddleware;
use Framework\Mode;
use Framework\Renderer;
use Framework\Router;
use Framework\Session\ErrorsManager;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Framework\Validator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

trait ControllerUtility
{
    /**
     * @var CookieInterface
     */
    protected $cookie;

    /**
     * Représente la page active
     * @var
     */
    protected $appPage;
    /**
     * @var SessionInterface
     */
    protected $session;
    /**
     * @var FlashService
     */
    protected $flash;

    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var ErrorsManager
     */
    private $errorsManager;


    /**
     * @var Validator|null
     */
    protected $errorsValidator;

    /**
     * @var array
     */
    protected $errorsValue;

    /**
     * @var array
     */
    private $activeTable;


    /**
     * @param ServerRequestInterface $request
     * @param ContainerInterface $container
     */
    public function initialize(ServerRequestInterface $request, ContainerInterface $container)
    {
        if (!Mode::is_cli()) {
            $this->renderer = $container->get(Renderer::class);
        }
        $this->router = $container->get(Router::class);
        $this->request = $request;
        $this->container = $container;
        $this->flash = $container->get(FlashService::class);
        $this->session = $this->container->get(SessionInterface::class);
        $this->cookie = $container->get(CookieInterface::class);
        $this->errorsManager = $container->get(ErrorsManager::class);
        $this->renderer->make([
            "appName" => $this->container->get("name")
        ]);
        $this->makeErrors();
        $this->activeTable = [];
    }

    protected function generateTokenCsrf(): string
    {
        $csrfMiddleware = $this->container->get(CsrfMiddleware::class);
        return $csrfMiddleware->generateToken();
    }

    /**
     * Génère un validator en fonction de la request
     * @return Validator
     */
    protected function validator(): Validator
    {
        return new Validator($this->request->getParsedBody());
    }

    /**
     * @param Validator $validator
     * @param array $body
     */
    protected function setErrors(Validator $validator, array $body): void
    {
        $this->errorsManager->setValues($body);
        $this->errorsManager->setValidator($validator);
    }

    /**
     * Passe les différentes erreurs à la vue si il y en a
     */
    protected function makeErrors()
    {
        $this->errorsValidator = $this->errorsManager->getValidator();
        $this->errorsValue = $this->errorsManager->getValues();
        $this->renderer->make([
            "values" => is_null($this->errorsValue) ? [] : $this->errorsValue,
            "errors" => !is_null($this->errorsValidator) ? $this->errorsValidator->getErrors() : []
        ]);
    }

    /**
     * @param string $pageName
     */
    protected function setPage(string $pageName): void
    {
        $this->renderer->make([
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
        return $this->router->generateUri($name, $params, $queryParams);
    }

    /**
     * @param string $name
     * @param array|null $params
     * @param array|null $queryParams
     * @return ResponseInterface
     */
    protected function redirect(string $name, ?array $params = [], ?array $queryParams = []): ResponseInterface
    {
        return $this->router->redirect($name, $params, $queryParams);
    }

    /**
     * @return EntityManager
     */
    protected function table():EntityManager
    {
        return $this->container->get(EntityManager::class);
    }

}