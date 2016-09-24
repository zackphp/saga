<?php

namespace Zack\Saga\Tests\Process;

use PHPUnit\Framework\TestCase;
use Zack\Saga\Effect\Effect;
use Zack\Saga\Exception\CancelSagaException;
use Zack\Saga\Exception\SagaException;
use Zack\Saga\Exception\UnknownEffectException;
use Zack\Saga\Process\Process;
use Zack\Saga\ProcessorInterface;

class ProcessTest extends TestCase
{
    const MESSAGE = 'message';

    public function testGeneratorHandling()
    {
        $processor = $this->createMock(ProcessorInterface::class);
        $startEffect = $this->createMock(Effect::class);
        $nextEffect = $this->createMock(Effect::class);
        $nullEffect = $this->createMock(Effect::class);

        $function = function() use ($startEffect, $nextEffect, $nullEffect) {
            yield $startEffect;
            yield $nextEffect;
            yield;
            yield $nullEffect;
            yield 'invalid value';
        };

        $process = new Process($processor, $function());

        $processor->expects($this->exactly(3))
            ->method('handle')
            ->withConsecutive(
                [$this->identicalTo($startEffect), $this->identicalTo($process)],
                [$this->identicalTo($nextEffect), $this->identicalTo($process)],
                [$this->identicalTo($nullEffect), $this->identicalTo($process)]
            );

        $process->start();
        $process->next();
        $process->next();

        $this->expectException(SagaException::class);

        $process->next();
    }

    public function testCancellation()
    {
        $processor = $this->createMock(ProcessorInterface::class);
        $startEffect = $this->createMock(Effect::class);
        $nextEffect = $this->createMock(Effect::class);
        $notHandledEffect = $this->createMock(Effect::class);

        $function = function() use ($startEffect, $nextEffect, $notHandledEffect) {
            try {
                yield $startEffect;
            } catch (CancelSagaException $exception) {
                yield $nextEffect;
            }

            yield $notHandledEffect;
        };

        $process = new Process($processor, $function());

        $processor->expects($this->exactly(2))
            ->method('handle')
            ->withConsecutive(
                [$this->identicalTo($startEffect), $this->identicalTo($process)],
                [$this->identicalTo($nextEffect), $this->identicalTo($process)]
            );

        $process->start();
        $process->cancel();
        $process->cancel();
    }

    public function testSend()
    {
        $processor = $this->createMock(ProcessorInterface::class);
        $sendEffect = $this->createMock(Effect::class);
        $nextEffect = $this->createMock(Effect::class);

        $function = function() use ($sendEffect, $nextEffect) {
            $message = yield $sendEffect;

            $this->assertSame(self::MESSAGE, $message);
            yield $nextEffect;
        };

        $process = new Process($processor, $function());

        $processor->expects($this->exactly(2))
            ->method('handle')
            ->withConsecutive(
                [$this->identicalTo($sendEffect), $this->identicalTo($process)],
                [$this->identicalTo($nextEffect), $this->identicalTo($process)]
            );

        $process->start();
        $process->send(self::MESSAGE);
        $process->next();
    }

    public function testHandleUnknownEffect()
    {
        $processor = $this->createMock(ProcessorInterface::class);
        $startEffect = $this->createMock(Effect::class);

        $function = function() use ($startEffect) {
            yield $startEffect;
        };

        $process = new Process($processor, $function());

        $processor->expects($this->once())
            ->method('handle')
            ->willThrowException(new UnknownEffectException('effect_name'));

        $this->expectException(SagaException::class);

        $process->start();
    }
}