<?php

namespace Zack\Saga\Process;

use Zack\Saga\Effect\Effect;
use Zack\Saga\Exception\CancelSagaException;
use Zack\Saga\Exception\SagaException;
use Zack\Saga\Exception\UnknownEffectException;
use Zack\Saga\ProcessorInterface;

final class Process implements ProcessInterface
{
    /** @var ProcessorInterface */
    private $processor;
    /** @var \Generator */
    private $generator;
    /** @var bool */
    private $running = false;

    /**
     * Process constructor.
     *
     * @param ProcessorInterface $processor
     * @param \Generator $generator
     */
    public function __construct(ProcessorInterface $processor, \Generator $generator)
    {
        $this->processor = $processor;
        $this->generator = $generator;
    }

    public function start()
    {
        $this->running = true;
        $this->generator->rewind();

        $this->current();
    }

    /**
     * @throws SagaException if yielded value is not an {@link Effect} or null.
     */
    public function current()
    {
        if (!$this->generator->valid()) {
            $this->running = false;

            return;
        }

        $effect = $this->generator->current();

        if ($effect === null) {
            $this->next();
            return;
        }

        if ($effect instanceof Effect) {
            $this->handle($effect);
            return;
        }

        throw new SagaException('A saga generator must yield effects or null.');
    }

    public function next()
    {
        try {
            $this->generator->next();
        } catch (\Exception $exception) {
            $this->running = false;

            throw $exception;
        }

        $this->current();
    }

    /**
     * @param Effect $effect
     * @throws SagaException if unknown effect was thrown.
     */
    private function handle(Effect $effect)
    {
        try {
            $this->processor->handle($effect, $this);
        } catch (UnknownEffectException $exception) {
            throw new SagaException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param mixed $value
     */
    public function send($value)
    {
        $this->generator->send($value);

        $this->current();
    }

    public function cancel()
    {
        try {
            $this->generator->throw(new CancelSagaException());
        } catch (CancelSagaException $exception) {
            // Fetch cancellation exception if not done in the process.
        }

        $this->current();
    }

    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        return $this->running;
    }

    /**
     * @return mixed
     */
    public function getReturn()
    {
        if ($this->running) {
            throw new SagaException('Cannot get return value of a running process.');
        }

        return $this->generator->getReturn();
    }
}