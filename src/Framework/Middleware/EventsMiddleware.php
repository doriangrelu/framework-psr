<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 11/11/2018
 * Time: 18:05
 */

namespace Framework\Middleware;


use App\Framework\Event\Emitter;
use Framework\Event\Logs;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class EventsMiddleware
{

    private $_container;

    public function __construct(ContainerInterface $container)
    {
        $this->_container = $container;
    }

    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return mixed
     * @throws \App\Framework\Event\DoubleEventException
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $eventManager= $this->_container->get(Emitter::class);
        $logsListener = $this->_container->get(Logs::class);
        $eventManager->addSubScriber($logsListener);
        $subscribers = $this->_container->get('events');
        foreach($subscribers as $subscriber){
            $eventManager->addSubScriber($this->_container->get($subscriber));
        }
        $eventManager->emit('on.access', $request);
        return $next($request);
    }
}