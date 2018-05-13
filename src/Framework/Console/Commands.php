<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 13/05/2018
 * Time: 11:00
 */

namespace App\Framework\Console;


class Commands
{
    private $method;
    private $params;
    private $root;

    private const DS = DIRECTORY_SEPARATOR;
    private const CONTROLLER = "Controllers".self::DS;
    private const MODELS = "";



    public function __construct(array $matches)
    {
        $this->method=$matches[2];
        $this->params = $matches[3];
        $this->root = dirname(dirname(__DIR__)).self::DS;
    }

    private function controller($name)
    {

    }

    public function models($name)
    {

    }


    public function run()
    {

    }
}