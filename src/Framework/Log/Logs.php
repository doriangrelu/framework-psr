<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 11/11/2018
 * Time: 18:18
 */

namespace Framework\Log;


class Logs
{

    const LOG_ACCESS = 'Access';
    /**
     * @var array
     */
    private static $_scopes = [
        'Access',
        'Error',
    ];

    /**
     * @param string $name
     */
    public static  function addScope(string $name)
    {
       self::$_scopes[] = $name;
    }

    /**
     * @param string $scope
     * @param string $body
     */
    public static function writte(string $scope, string $body): void
    {
        //throw new \Exception();
        $fileName = LOGS . $scope . '.txt';
        self::_checkFileSize($fileName);
        $text = date('Y-m-d H:i:s') . ': ' . $body;
        file_put_contents($fileName, $text . PHP_EOL, FILE_APPEND);
    }

    /**
     * @param $fileName
     */
    private static function _checkFileSize($fileName): void
    {
        if (file_exists($fileName) && is_file($fileName) && filesize($fileName) >= 100000) {
            rename($fileName, $fileName . '.' . time());
        }
    }

}