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
    const DEVELOPPEMENT='DEV';
    const PRODUCTION='PROD';

    /**
     * @var string
     */
    private static $mode=self::DEVELOPPEMENT;

    /**
     * @param string $mode
     */
    public static function init(string $mode){
        static::setMode($mode);
    }

    /**
     * @return bool
     */
    public static function is_dev():bool{
        return self::$mode==self::DEVELOPPEMENT;
    }

    /**
     * @return bool
     */
    public static function is_prod():bool{
        return self::$mode==self::PRODUCTION;
    }

    /**
     * @return String
     */
    public static function getMode():String{
        return self::$mode;
    }

    /**
     * @return bool
     */
    public static function is_cli():bool{
        return php_sapi_name()=="cli";
    }

    /**
     * @param string $mode
     * @throws \Exception
     */
    public static function setMode(string $mode):void{
        if($mode==self::DEVELOPPEMENT || $mode==self::PRODUCTION){
            self::$mode=$mode;
        } else {
            throw new \Exception("Le mode <$mode> n'existe pas.");
        }
    }


}