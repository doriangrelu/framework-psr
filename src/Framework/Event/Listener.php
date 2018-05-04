<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 01/05/2018
 * Time: 17:27
 */

namespace App\Framework\Event;


class Listener
{
    /**
     * @var int
     */
    private $calls = 0;
    /**
     * @var Callable
     */
    private $callback;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var bool
     */
    private $once = false;

    /**
     * Stop la propagation
     * @var bool
     */
    private $stopPropagation = false;

    /**
     * Listener constructor.
     * @param Callable $callback
     * @param int $priority
     * @param bool $once
     */
    public function __construct(Callable $callback, int $priority)
    {
        $this->callback = $callback;
        $this->priority = $priority;
    }

    /**
     * @param array $args
     * @return mixed
     */
    public function handle(array $args = [])
    {
        if ($this->once && $this->calls > 0) {
            return null;
        }
        $this->calls++;
        return call_user_func_array($this->callback, $args);
    }

    /**
     * @return Listener
     */
    public function once(): self
    {
        $this->once = true;
        return $this;
    }

    public function stopPropagation(): self
    {
        $this->stopPropagation = true;
        return $this;
    }

    /**
     * @return Callable
     */
    public function getCallback(): Callable
    {
        return $this->callback;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return bool
     */
    public function isStopPropagation(): bool
    {
        return $this->stopPropagation;
    }



}