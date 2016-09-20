<?php

namespace Zack\Saga\Effect;

use Symfony\Component\EventDispatcher\Event;
use Zack\Saga\Effects;

final class DispatchEffect extends Effect
{
    /** @var string */
    private $eventName;
    /** @var Event */
    private $event;

    /**
     * PutEffect constructor.
     * @param string $eventName
     * @param Event $event
     */
    public function __construct(string $eventName, Event $event)
    {
        $this->eventName = $eventName;
        $this->event = $event;
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return $this->eventName;
    }

    /**
     * @return Event
     */
    public function getEvent(): Event
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return Effects::DISPATCH;
    }
}