<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 22/11/17
 * Time: 14:33
 */

namespace App\Bundle\Errors;


use App\Bundle\Bundle;
use Framework\Database\Migrations;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class ErrorsBundle extends Bundle
{
    public function __construct()
    {
        parent::__construct();
    }
}