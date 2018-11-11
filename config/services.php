<?php

use Framework\Middleware\CsrfMiddleware;
use App\Framework\Auth\Auth;
use App\Framework\Auth\AuthInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Framework\Cookie\CookieInterface;
use Framework\Cookie\PHPCookie;
use Framework\Mode;
use Framework\Router;
use Framework\Router\RouterInterface;
use Framework\Session\PHPSession;
use Framework\Session\SessionInterface;
use Psr\Container\ContainerInterface;

return [
    CookieInterface::class => \DI\object(PHPCookie::class),
    SessionInterface::class => \DI\object(PHPSession::class),
    CsrfMiddleware::class => \DI\object()->constructor(\DI\get(SessionInterface::class), \DI\get(CookieInterface::class)),
    RouterInterface::class => \DI\object(Router::class),
    AuthInterface::class => \DI\object(Auth::class),
    \Framework\Auth\UserInterface::class => \DI\object(\Framework\Auth\User::class),
    \PDO::class => function (ContainerInterface $c) {
        return new PDO(
            'mysql:host=' . $c->get('database.host') . ';dbname=' . $c->get('database.name'),
            $c->get('database.username'),
            $c->get('database.password'),
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    },
    EntityManager::class => function (ContainerInterface $c) {
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
    },
    FluentPDO::class => DI\object()->constructor(DI\get(\PDO::class)),

];