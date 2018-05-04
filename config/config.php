<?php


use Framework\Auth\AuthInterface;
use Framework\Auth\DatabaseAuth;
use Framework\Cookie\CookieInterface;
use Framework\Cookie\PHPCookie;
use Framework\Mailer\Mailer;
use Framework\Middleware\CsrfMiddleware;
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

return [
    //System routes
    'app.redirect.conexion'=>"membre.home",
    'app.connexion'=>"form.connexion",

    'cookie.path'=>dirname(__DIR__).DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."cookies".DIRECTORY_SEPARATOR,

    //Application
    'app.name' => "Auto-entrepreneur",
    'security.salt' => "hdNdhcvvgSGDdhncjd",
    'locale' => 'fr_FR',
    'lang' => 'fr',
    'ds' => DIRECTORY_SEPARATOR,
    'env' => \DI\env('ENV', 'production'),
    //Database
    'database.host' => 'localhost',
    'database.username' => 'homestead',
    'database.password' => 'secret',
    'database.name' => 'homestead',
    //SMTP config
    'mail.host'=>"localhost",
    'mail.port'=>1025,
    'mail.username'=>null,
    'mail.password'=>null,
    'mail.from'=>["doriangrelu@gmail.com"=>"Dorian GRELU"],
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
    //Mailer::class=>\DI\object(Mailer::class)->constructor(\Psr\Container\ContainerInterface::class),
    CookieInterface::class=> \DI\object(PHPCookie::class),
    SessionInterface::class => \DI\object(PHPSession::class),
    CsrfMiddleware::class => \DI\object()->constructor(\DI\get(SessionInterface::class), \DI\get(CookieInterface::class)),
    RouterInterface::class => \DI\object(Router::class),
    ErrorsManager::class=>\DI\object(ErrorsManager::class),
    \PDO::class => function (\Psr\Container\ContainerInterface $c) {
        return new PDO(
            'mysql:host=' . $c->get('database.host') . ';dbname=' . $c->get('database.name'),
            $c->get('database.username'),
            $c->get('database.password'),
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }
];