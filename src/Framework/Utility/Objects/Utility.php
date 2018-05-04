<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 01/12/2017
 * Time: 19:28
 */

namespace Framework\Utility\Objects;


class Utility
{
    public function __construct()
    {
    }

    public function generateToken(int $length=16)
    {
        return bin2hex(random_bytes($length));
    }

    public function randomString(int $length = 10, bool $withNumber = false, bool $withUpper=false): string
    {
        $string = null;
        $alphabet=[];
        foreach(range("a", "z") as $i){
            $alphabet[]=$i;
        }
        for ($i = 0; $i < $length; $i++) {
            $rand=$alphabet[rand(0, (count($alphabet)-1))];

            if($withNumber){
                if(rand(1,4)==1){
                    $string.=rand(0,9);
                } else {
                    if($withUpper){
                        $string.=(rand(1,4)==1?strtoupper($rand):$rand);
                    }
                }
            } else {
                if($withUpper){
                    $string.=(rand(1,4)==1?strtoupper($rand):$rand);
                } else {
                    $string.=$rand;
                }
            }
        }
        return $string;
    }

}