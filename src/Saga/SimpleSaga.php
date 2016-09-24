<?php

namespace Zack\Saga;

use Zack\Saga\Exception\SagaException;

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
        $generator = ($this->callback)();

        if (!$generator instanceof \Generator) {
            throw new SagaException('A saga must return a generator yielding effects.');
        }

        yield from $generator;

        try {
            return $generator->getReturn();
        } catch (\Exception $exception) {
            // This is a simple try and catch to "clone" the return behavior of the callback.
        }
    }
}