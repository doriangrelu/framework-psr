<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 01/12/17
 * Time: 12:09
 */

namespace App\Bundle\Pages\Controller;


use App\Bundle\Pages\PagesBundle;
use Framework\Cookie\CookieInterface;
use Framework\Validator;

class Pages extends PagesBundle
{
    public function index()
    {

        return "dorian";
    }

    public function presentation()
    {
        //return $this->renderer->render("index");
    }
}