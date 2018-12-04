<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 02/12/2018
 * Time: 15:56
 */

namespace Framework\Utility;


use App\Framework\Exception\ClassUtilityException;

trait ClassUtility
{

    /**
     * @param $object
     * @param $class
     * @param null $exception
     * @return bool
     */
    public function isInstanceOf($object, $class, $exception = null): bool
    {
        $status = $object instanceof $class;
        if (!$status && $exception !== null) {
            throw new $exception;
        }
        return $status;
    }

}