<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 17/05/2018
 * Time: 18:52
 */

namespace App\Event;

use App\Framework\Event\SubScriberInterface;
use GuzzleHttp\Psr7\Response;
use function Http\Response\send;

class ErrorHandler implements SubScriberInterface
{

    public function __construct()
    {
    }

    /**
     * @return array
     */
    public function getEvents(): array
    {
       return [
           "on.404"=>"notFound"
       ];
    }

    public function notFound()
    {
        send(new Response(404, [], "Error 404"));
    }

    private function getMessageFromException(\Exception $e)
    {
        if(empty($e->getMessage())){
            return "Error triggered by Error Handler";
        }
        return "Message: {$e->getMessage()}";
    }

}