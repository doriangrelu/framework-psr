<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 22/11/17
 * Time: 16:42
 */

namespace Framework\Database;


class Migrations
{
    public static function getMigrationFolder(string $className):?string{
        $migration=\BUNDLE.$className.DS."Database".DS."Migrations";
        if(is_dir($migration)){
            return $migration;
        }
        return null;
    }
    public static function getSeedsFolder(string $className):?string{
        $seeds=\BUNDLE.$className.DS."Database".DS."Seeds";
        if(is_dir($seeds)){
            return $seeds;
        }
        return null;
    }
}