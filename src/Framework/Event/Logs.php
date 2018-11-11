<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 11/11/2018
 * Time: 18:04
 */

namespace Framework\Event;


use App\Framework\Event\SubScriberInterface;
use Psr\Http\Message\ServerRequestInterface;

class Logs implements SubScriberInterface
{


    /**
     * @return array
     */
    public function getEvents(): array
    {
        return [
            'on.access' => 'accessLog',
        ];
    }

    public function accessLog(ServerRequestInterface $request)
    {
        $body = $_SERVER['REMOTE_ADDR'] . ' access to ' . $request->getUri()->getHost() . $request->getUri()->getPath() . '?'. $request->getUri()->getQuery();
       \Framework\Log\Logs::writte(\Framework\Log\Logs::LOG_ACCESS, $body);
    }

    // Function to get the client ip address
    private function _getIp()
    {
        $ipaddress = '';
        if ($_SERVER['HTTP_CLIENT_IP'])
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if ($_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if ($_SERVER['HTTP_X_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if ($_SERVER['HTTP_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if ($_SERVER['HTTP_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if ($_SERVER['REMOTE_ADDR'])
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }

}