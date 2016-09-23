<?php

namespace Zack\Saga\Tests\EffectHandler;

use PHPUnit\Framework\TestCase;
use Zack\Saga\Effect\ForkEffect;
use Zack\Saga\EffectHandler\ForkEffectHandler;
use Zack\Saga\Effects;
use Zack\Saga\Process\ProcessInterface;
use Zack\Saga\Process\Task;
use Zack\Saga\Processor;
use Zack\Saga\SagaInterface;

class ForkEffectHandlerTest extends TestCase
{
    public function testGetEffectHandler()
    {
        $processor = $this->createMock(Processor::class);
        $effectHandler = new ForkEffectHandler($processor);

        $this->assertEquals($effectHandler->getEffectHandlers(), [
            Effects::FORK => 'handleFork'
        ]);
    }

    public function testHandleFork()
    {
        $saga = $this->createMock(SagaInterface::class);
        $effect = new ForkEffect($saga);

        $processor = $this->createMock(Processor::class);
        $effectHandler = new ForkEffectHandler($processor);

        $process = $this->createMock(ProcessInterface::class);

        $task = new Task($process);

        $processor->expects($this->once())
            ->method('run')
            ->with($this->identicalTo($saga))
            ->willReturn($task);

        $process->expects($this->once())
            ->method('send')
            ->with($this->identicalTo($task));

        $effectHandler->handleFork($effect, $process);
    }
}