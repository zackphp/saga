<?php

namespace Zack\Saga;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zack\Saga\Effect\Effect;
use Zack\Saga\EffectHandler\EffectHandlerInterface;
use Zack\Saga\EffectHandler\EventEffectHandler;
use Zack\Saga\EffectHandler\ForkEffectHandler;
use Zack\Saga\Exception\DuplicateEffectHandlerException;
use Zack\Saga\Exception\SagaException;
use Zack\Saga\Exception\UnknownEffectException;
use Zack\Saga\Process\Process;
use Zack\Saga\Process\ProcessInterface;

/**
 * Saga Processor.
 *
 * Can be used to run sagas and manages effect handlers.
 *
 * @package Zack\Saga
 */
class Processor implements ProcessorInterface
{
    /** @var EffectHandlerInterface[] */
    private $handlers = [];

    /**
     * @param EffectHandlerInterface $effectHandler
     * @throws DuplicateEffectHandlerException if effect handler tries to register for effects a handler is already registered for.
     */
    public function addEffectHandler(EffectHandlerInterface $effectHandler)
    {
        $effectHandlers = $effectHandler->getEffectHandlers();

        foreach ($effectHandlers as $effectName => $handler) {
            if (isset($this->handlers[$effectName])) {
                throw new DuplicateEffectHandlerException($effectName);
            }

            $this->handlers[$effectName] = [$effectHandler, $handler];
        }
    }

    /**
     * @param string $effectName
     * @return EffectHandlerInterface
     * @throws UnknownEffectException if there is no effect handler for the given effect name.
     */
    public function getEffectHandler(string $effectName)
    {
        if (!isset($this->handlers[$effectName])) {
            throw new UnknownEffectException($effectName);
        }

        return $this->handlers[$effectName];
    }

    /**
     * @param SagaInterface|callable $saga
     */
    public function run($saga)
    {
        $saga = self::createSaga($saga);

        $generator = $saga->run();

        $process = new Process($this, $generator);
        $process->start();
    }

    /**
     * @param Effect $effect
     * @param ProcessInterface $process
     */
    public function handle(Effect $effect, ProcessInterface $process)
    {
        $effectName = $effect->getName();
        $handler = $this->getEffectHandler($effectName);

        call_user_func($handler, $effect, $process);
    }

    /**
     * @param EventDispatcherInterface|null $eventDispatcher
     * @return Processor
     * @codeCoverageIgnore
     */
    public static function create(EventDispatcherInterface $eventDispatcher = null)
    {
        if ($eventDispatcher === null) {
            $eventDispatcher = new EventDispatcher();
        }

        $processor = new self();

        $processor->addEffectHandler(new EventEffectHandler($eventDispatcher));
        $processor->addEffectHandler(new ForkEffectHandler($processor));

        return $processor;
    }

    /**
     * @param SagaInterface|callable $saga
     * @return SagaInterface
     * @throws SagaException if the given saga is not an instance of Saga or a callable.
     */
    public static function createSaga($saga): SagaInterface
    {
        if ($saga instanceof SagaInterface) {
            return $saga;
        }

        if (is_callable($saga)) {
            return new SimpleSaga($saga);
        }

        throw new SagaException('Saga must be a function or a Saga instance.');
    }
}