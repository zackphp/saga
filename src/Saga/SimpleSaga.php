<?php

namespace Zack\Saga;

class SimpleSaga implements SagaInterface
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * SimpleSaga constructor.
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return \Generator
     */
    public function run(): \Generator
    {
        yield from ($this->callback)();
    }
}