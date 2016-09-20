<?php

namespace Zack\Saga;

use Symfony\Component\EventDispatcher\Event;
use Zack\Saga\Effect\ForkEffect;
use Zack\Saga\Effect\DispatchEffect;
use Zack\Saga\Effect\TakeEffect;

final class Effects
{
    const FORK = 'fork';
    const TAKE = 'take';
    const DISPATCH = 'dispatch';

    /**
     * Shorthand for creating take effects.
     *
     * @param callable|string $eventName
     * @param callable|null $pattern
     * @return TakeEffect
     */
    public static function take(string $eventName, callable $pattern = null)
    {
        return new TakeEffect($eventName, $pattern);
    }

    /**
     * Shorthand for creating dispatch effects.
     *
     * @param string $eventName
     * @param Event $event
     * @return DispatchEffect
     */
    public static function dispatch(string $eventName, Event $event)
    {
        return new DispatchEffect($eventName, $event);
    }

    /**
     * Shorthand for creating fork effects.
     *
     * @param SagaInterface|callable $saga
     * @return ForkEffect
     */
    public static function fork($saga)
    {
        return new ForkEffect(Processor::createSaga($saga));
    }
}