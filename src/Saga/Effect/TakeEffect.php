<?php

namespace Zack\Saga\Effect;

use Zack\Saga\Effects;

final class TakeEffect extends Effect
{
    /** @var string */
    private $eventName;
    /** @var callable */
    private $pattern;

    /**
     * TakeEffect constructor.
     * @param string $actionName
     * @param callable $pattern
     */
    public function __construct(string $actionName, callable $pattern = null)
    {
        $this->eventName = $actionName;
        $this->pattern = $pattern;
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return $this->eventName;
    }

    /**
     * @return null|callable
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return Effects::TAKE;
    }
}