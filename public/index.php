<?php

use App\Bundle\Auth\AuthBundle;
use App\Bundle\Errors\ErrorsBundle;
use App\Bundle\Factures\FacturesBundle;
use App\Bundle\Membres\MembresBundle;
use App\Bundle\Pages\PagesBundle;
use App\Bundle\Parametres\ParametresBundle;
use App\Bundle\Ressources\RessourcesBundle;
use App\Framework\Middleware\AttachMiddleware;
use Framework\Middleware\CsrfMiddleware;
use Framework\Middleware\DispatcherMiddleware;
use Framework\Middleware\LoggedInMiddleware;
use Framework\Middleware\MethodMiddleware;
use Framework\Middleware\RouterMiddleware;
use Framework\Middleware\TrailingSlashMiddleware;
use Framework\Middleware\NotFoundMiddleware;
use Framework\Mode;
use GuzzleHttp\Psr7\ServerRequest;
use Middlewares\Whoops;

chdir(dirname(__DIR__));

header('Content-Type: text/html; charset=utf-8');

require 'vendor/autoload.php';
require 'config/functions.php';
require 'config/constants.php';

Mode::init(Mode::DEVELOPPEMENT);

$app = (new \Framework\App());


if (php_sapi_name() !== "cli") {
    try {
        $response = $app->run(ServerRequest::fromGlobals());
        \Http\Response\send($response);
    } catch (Exception $e) {
        dump($e);
    }
}
