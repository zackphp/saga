<?php

use Symfony\Component\EventDispatcher\Event;
use Zack\Saga\Effects;

if (!function_exists('take')) {
    /**
     * Shorthand for {@link Effects::take}.
     *
     * @param string $eventName
     * @param callable|null $pattern
     * @return \Zack\Saga\Effect\TakeEffect
     */
    function take(string $eventName, callable $pattern = null)
    {
        return Effects::take($eventName, $pattern);
    }
}

if (!function_exists('dispatch')) {
    /**
     * Shorthand for {@link Effects::dispatch}.
     *
     * @param string $eventName
     * @param Event $event
     * @return \Zack\Saga\Effect\DispatchEffect
     */
    function dispatch(string $eventName, Event $event)
    {
        return Effects::dispatch($eventName, $event);
    }
}

if (!function_exists('fork')) {
    /**
     * Shorthand for {@link Effects::fork}.
     *
     * @param $saga
     * @return \Zack\Saga\Effect\ForkEffect
     */
    function fork($saga)
    {
        return Effects::fork($saga);
    }
}
