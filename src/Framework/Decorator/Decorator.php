<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 02/12/2018
 * Time: 15:45
 */

namespace Framework\Decorator;


use Framework\Utility\ReflectorUtility;

class Decorator implements DecoratorInterface
{

    use ReflectorUtility;

    public function getBaseClassName(): string
    {
        return $this->getClassName();
    }
}