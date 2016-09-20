<?php

namespace Zack\Saga\EffectHandler;

use Zack\Saga\Effect\ForkEffect;
use Zack\Saga\Effects;
use Zack\Saga\Process\ProcessInterface;
use Zack\Saga\Process\Task;
use Zack\Saga\ProcessorInterface;

final class ForkEffectHandler implements EffectHandlerInterface
{
    /** @var ProcessorInterface */
    private $processor;

    /**
     * ForkEffectHandler constructor.
     * @param ProcessorInterface $processor
     */
    public function __construct(ProcessorInterface $processor)
    {
        $this->processor = $processor;
    }

    /**
     * @return array
     */
    public function getEffectHandlers()
    {
        return [
            Effects::FORK => 'handleFork'
        ];
    }

    /**
     * @param ForkEffect $effect
     * @param ProcessInterface $process
     */
    public function handleFork(ForkEffect $effect, ProcessInterface $process)
    {
        $saga = $effect->getSaga();
        $task = new Task($process);

        $this->processor->run($saga);
        $process->send($task);
    }
}