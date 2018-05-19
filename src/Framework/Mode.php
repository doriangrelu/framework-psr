<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 22/11/17
 * Time: 14:43
 */

namespace Framework;




class Mode
{
    const DEVELOPPEMENT=1;
    const PRODUCTION=2;

    /**
     * @return bool
     */
    public static function is_cli():bool{
        return php_sapi_name()=="cli";
    }


}