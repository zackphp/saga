<?php

namespace Zack\Saga\EffectHandler;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zack\Saga\Effect\DispatchEffect;
use Zack\Saga\Effect\TakeEffect;
use Zack\Saga\Effects;
use Zack\Saga\Process\ProcessInterface;

final class EventEffectHandler implements EffectHandlerInterface
{
    /** @var EventDispatcherInterface */
    private $dispatcher;

    /**
     * ActionEffectHandler constructor.
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return array
     */
    public function getEffectHandlers(): array
    {
        return [
            Effects::TAKE => 'handleTake',
            Effects::DISPATCH => 'handleDispatch'
        ];
    }

    /**
     * @param TakeEffect $effect
     * @param ProcessInterface $process
     */
    public function handleTake(TakeEffect $effect, ProcessInterface $process)
    {
        $eventName = $effect->getEventName();
        $pattern = $effect->getPattern();

        $listener = function (Event $event) use ($pattern, $process, $eventName, &$listener) {
            if ($pattern === null || $pattern($event)) {
                $this->dispatcher->removeListener($eventName, $listener);
                $process->send($event);
            }
        };

        $this->dispatcher->addListener($eventName, $listener);
    }

    /**
     * @param DispatchEffect $effect
     * @param ProcessInterface $process
     */
    public function handleDispatch(DispatchEffect $effect, ProcessInterface $process)
    {
        $eventName = $effect->getEventName();
        $event = $effect->getEvent();

        $this->dispatcher->dispatch($eventName, $event);
        $process->next();
    }
}