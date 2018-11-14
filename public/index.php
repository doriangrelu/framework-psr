<?php

use Framework\App;
use Framework\Mode;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;

chdir(dirname(__DIR__));

header('Content-Type: text/html; charset=utf-8');

require 'vendor/autoload.php';
require 'config/functions.php';
require 'config/constants.php';


if (php_sapi_name() !== "cli") {
    $app = new App();
    if (App::container()->get("mode") === Mode::DEVELOPPEMENT) {
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->sendHttpCode(500);
        $whoops->register();

        $response = $app->run(ServerRequest::fromGlobals());
        \Http\Response\send($response);
    } else {
        $errorHandler = new \App\Event\ErrorHandler();
        $errorHandler->register();
        $response = $app->run(ServerRequest::fromGlobals());
        \Http\Response\send($response);
    }
} else {
    $app = new App();
}
