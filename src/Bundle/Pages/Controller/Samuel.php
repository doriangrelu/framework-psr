<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 10/01/2018
 * Time: 10:24
 */

namespace App\Bundle\Pages\Controller;


use App\Bundle\Pages\PagesBundle;
use GuzzleHttp\Psr7\Response;

class Samuel extends PagesBundle
{
    public function bonjour($prenom)
    {
        return new Response(200, [], "Bonjour $prenom !");
    }
}