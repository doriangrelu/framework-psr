<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 17/05/2018
 * Time: 18:25
 */

namespace App\Controllers;

use App\Framework\Auth\AuthInterface;
use App\Framework\Controller;
use Doctrine\ORM\EntityManager;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class BaseController extends Controller
{

    public function setSecurity(): void
    {
        $this->getAuth()->allowController();
    }

    public function index()
    {
        $repository = $this->table()->getRepository("Phinxlog");
        dd($repository->getTest() . "test");

        return "Bienvenue";
    }

}