<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 01/05/2018
 * Time: 17:14
 */

namespace App\Framework\Event;


class Emitter
{

    /**
     * @var Listener[][]
     */
    private $events = [];

    public function __construct()
    {

    }

    /**
     * @param SubScriberInterface $subScriber
     * @throws DoubleEventException
     */
    public function addSubScriber(SubScriberInterface $subScriber)
    {
        $events = $subScriber->getEvents();
        foreach($events as $event=>$method){
            $this->on($event, [$subScriber, $method]);
        }
    }

    /**
     * @param string $key
     * @param callable $callable
     * @param int $priority
     * @return Listener
     * @throws DoubleEventException
     */
    public function on(string $key, callable $callable, $priority = 0): Listener
    {
        if (!$this->hasListenner($key)) {
            $this->events[$key] = [];
        }
        $this->checkDoubleCallableForEvent($key, $callable);
        $listener = new Listener($callable, $priority);
        $this->events[$key][] = $listener;
        $this->sortListeners($key);

        return $listener;
    }

    /**
     * @param string $key
     * @param callable $callable
     * @param int $priority
     * @return Listener
     */
    public function once(string $key, callable $callable, $priority = 0): Listener
    {
        return $this->on($key, $callable, $priority)->once();
    }

    /**
     * @param string $key
     */
    private function sortListeners(string $key): void
    {
        uasort($this->events[$key], function ($a, $b) {
            return $a->getPriority() < $b->getPriority();
        });
    }

    /**
     * @param string $key
     * @return bool
     */
    private function hasListenner(string $key): bool
    {
        return isset($this->events[$key]);
    }

    /**
     * @param string $key
     * @param callable $callback
     * @return bool
     * @throws DoubleEventException
     */
    private function checkDoubleCallableForEvent(string $key, callable $callback): bool
    {
        foreach ($this->events[$key] as $listener) {
            if ($listener === $callback) {
                throw new DoubleEventException();
            }
        }
        return false;
    }

    /**
     * @param string $key
     * @param mixed ...$args
     * @return mixed
     */
    public function emit(string $key, ...$args)
    {
        if (isset($this->events[$key])) {
            foreach ($this->events[$key] as $listener) {
                $listener->handle($args);
                if ($listener->isStopPropagation()) {
                    return null;
                }
            }
        }
        return null;
    }

}