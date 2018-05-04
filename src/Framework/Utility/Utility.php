<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 30/11/17
 * Time: 16:16
 */

namespace Framework\Utility;


use Framework\Utility\Objects\Password;
use ICanBoogie\Inflector;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Utility\Inflector as CakeInflector;

trait Utility
{

    /**
     * @var Password
     */
    protected $password;

    /**
     * @var Inflector
     */
    protected $inflector;

    /**
     * @var \Framework\Utility\Objects\Utility
     */
    protected $utility;

    /**
     * @param ContainerInterface $container
     */
    public function setProperties(ContainerInterface $container): void
    {
        $this->password = new Password($container);
        if ($container->has("lang")) {
            $lang = $container->get("lang");
        } else {
            $lang = Inflector::DEFAULT_LOCALE;
        }
        $this->inflector = Inflector::get($lang);
        $this->utility=new \Framework\Utility\Objects\Utility();
    }





    /**
     * Génère un slug via cake inflector
     * @param string $string
     * @return string
     */
    protected function slug(string $string): string
    {
        return CakeInflector::slug($string);
    }



}