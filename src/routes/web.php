<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 01/05/2018
 * Time: 15:29
 */

use Framework\Router\RouterFactory;
use App\Framework\Facades\Route;

Route::get("/", function () {
    return "dorian";
}, "test");

Route::scope("admin", function (RouterFactory $routes) {
    $routes->get("/", function () {
        return "dorian";
    }, "test2");
}, [\App\Middlewares\TestMiddleware::class]);