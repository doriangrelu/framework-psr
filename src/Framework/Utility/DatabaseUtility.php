<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 02/12/2017
 * Time: 12:45
 */

namespace Framework\Utility;


trait DatabaseUtility
{
    public function setAutoInscrement(string $table, ?int $rank=0):string
    {
        return "ALTER TABLE `$table` AUTO_INCREMENT=$rank";
    }
}