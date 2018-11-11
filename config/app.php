<?php

use App\Event\ErrorHandler;
use App\Framework\Auth\Auth;
use App\Framework\Auth\AuthInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Framework\Cookie\CookieInterface;
use Framework\Cookie\PHPCookie;
use Framework\Middleware\CsrfMiddleware;
use Framework\Middleware\DispatcherMiddleware;
use Framework\Middleware\EventsMiddleware;
use Framework\Middleware\MethodMiddleware;
use Framework\Middleware\NotFoundMiddleware;
use Framework\Middleware\RouterMiddleware;
use Framework\Middleware\TrailingSlashMiddleware;
use Framework\Mode;
use Framework\Router;
use Framework\Router\RouterInterface;
use Framework\Session\ErrorsManager;
use Framework\Session\PHPSession;
use Framework\Session\SessionInterface;
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
use Psr\Container\ContainerInterface;

return [

    /**
     * App Configuration
     */
    "app" => [
        "name" => "Application Name Here",
        "mode" => Mode::DEVELOPPEMENT,
        "auth" => [
            "userTable" => "",
            "rolesTable" => "",
            "tokenSecurity" => true
        ],
        "seed" => dirname(__DIR__) . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "Database" . DIRECTORY_SEPARATOR . "Seed" . DIRECTORY_SEPARATOR
    ],

    /**
     * Auth Configuration
     */
    "Auth" => [
        "Auth.actived"=>true, // Active module
        "Auth.Exception" => false, //Exception if is forbidden
        "Auth.route.redirectLogin" => "", // If forbidden route name redirection to login page
        "Auth.user" => "", //Implemention of App\Framework\Auth\UserInterface
    ],



    /**
     * Database définition
     */
    "database" => [
        'database.host' => 'localhost',
        'database.username' => 'root',
        'database.password' => '',
        'database.name' => 'doriangrelu'
    ],

    /**
     * Mail Configuration
     */
    "mailer" => [
        'mail.host' => "localhost",
        'mail.port' => 1025,
        'mail.username' => null,
        'mail.password' => null,
        'mail.from' => ["doriangrelu@gmail.com" => "Dorian GRELU"]
    ],

    /**
     * TWIG Moduls définition
     */
    'twig.extensions' => [
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
    ],

    /**
     * Middleware Définition for Application
     */
    "middlewares" => [
        TrailingSlashMiddleware::class,
        EventsMiddleware::class,
        MethodMiddleware::class,
        CsrfMiddleware::class,
        RouterMiddleware::class,
        DispatcherMiddleware::class,
        NotFoundMiddleware::class,
        Whoops::class
    ],
];