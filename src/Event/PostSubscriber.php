<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 01/05/2018
 * Time: 18:32
 */

namespace App\Event;


use App\Framework\Event\SubScriberInterface;

class PostSubscriber implements SubScriberInterface
{

    /**
     * @return array
     */
    public function getEvents(): array
    {
        return [
            "on.update.post" => "onUpdatePost"
        ];
    }

    /**
     * @param $a
     * @param $b
     * @throws \Exception
     */
    public function onUpdatePost($a, $b){
        if($a!==$b){
            throw new \Exception("test");
        }
    }

}