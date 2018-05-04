<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 22/11/17
 * Time: 14:32
 */

namespace App\Bundle\Errors\Controller;


use App\Bundle\Errors\ErrorsBundle;

class Errors extends ErrorsBundle
{
    public function __construct()
    {
        parent::__construct();

    }

    public function notFound($request){

        return $this->renderer->render("NotFound");
    }
}