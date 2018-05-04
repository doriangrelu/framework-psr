<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 04/01/2018
 * Time: 14:50
 */

namespace App\Bundle\Ressources;


use App\Bundle\Bundle;
use App\Bundle\Ressources\Controller\FileReader;
use Framework\Router;

class RessourcesBundle extends Bundle
{
    public static function routes(Router $router): void
    {
        $router->get("css/:file", [FileReader::class, "cssLocal"], "css.local");
    }
}