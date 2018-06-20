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
        $whoops->register();

        $response = $app->run(ServerRequest::fromGlobals());
        \Http\Response\send($response);
    } else {
        try {
            $response = $app->run(ServerRequest::fromGlobals());
            \Http\Response\send($response);
        } catch (Exception $e) {
            \Http\Response\send(new Response(500, [], "Internal Error please contact administrator"));
        }
    }
} else {
    $app = new App();
}
