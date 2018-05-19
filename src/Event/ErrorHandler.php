<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 17/05/2018
 * Time: 18:52
 */

namespace App\Event;


use App\Framework\Event\SubScriberInterface;
use Framework\App;
use GuzzleHttp\Psr7\Response;
use function Http\Response\send;
use Whoops\Run;

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
           "on.error"=>"errorHandler"
       ];
    }

    public function errorHandler(\Exception $e){
        /*ob_start();
        dump($e);
        $error=ob_get_clean();
        send(new Response(500, [], $error));*/

        trigger_error($this->getMessageFromException($e), E_USER_ERROR);
    }

    private function getMessageFromException(\Exception $e)
    {
        if(empty($e->getMessage())){
            return "Error triggered by Error Handler";
        }
        return "Message: {$e->getMessage()}";
    }

}