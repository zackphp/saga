<?php

namespace Zack\Saga\Tests;

use PHPUnit\Framework\TestCase;
use Zack\Saga\Effect\Effect;
use Zack\Saga\EffectHandler\EffectHandlerInterface;
use Zack\Saga\Exception\DuplicateEffectHandlerException;
use Zack\Saga\Exception\SagaException;
use Zack\Saga\Exception\UnknownEffectException;
use Zack\Saga\Process\ProcessInterface;
use Zack\Saga\Processor;
use Zack\Saga\SagaInterface;
use Zack\Saga\SimpleSaga;

class ProcessorTest extends TestCase
{
    const EFFECT_NAME = 'test_effect';
    const EFFECT_HANDLER_METHOD = 'handleTestEffect';

    private function createEffectHandler()
    {
        $effectHandler = $this->getMockBuilder(EffectHandlerInterface::class)
            ->setMethods(['getEffectHandlers', self::EFFECT_HANDLER_METHOD])
            ->getMock();

        $effectHandler->expects($this->atLeastOnce())
            ->method('getEffectHandlers')
            ->willReturn([
                self::EFFECT_NAME => self::EFFECT_HANDLER_METHOD
            ]);

        return $effectHandler;
    }

    public function testAddEffectHandler()
    {
        $processor = new Processor();
        $effectHandler = $this->createEffectHandler();

        $processor->addEffectHandler($effectHandler);
        $callback = $processor->getEffectHandler(self::EFFECT_NAME);

        $this->assertEquals($callback, [$effectHandler, self::EFFECT_HANDLER_METHOD]);

        $this->expectException(DuplicateEffectHandlerException::class);
        $processor->addEffectHandler($effectHandler);
    }

    public function testUnknownEffect()
    {
        $processor = new Processor();

        $this->expectException(UnknownEffectException::class);
        $processor->getEffectHandler('unknown_effect');
    }

    public function testRunSaga()
    {
        $processor = new Processor();

        $saga = $this->createMock(SagaInterface::class);
        /** @var \Generator $generator */
        $generator = (function(): \Generator {
            yield;
        })();

        $saga->expects($this->once())
            ->method('run')
            ->willReturn($generator);

        $processor->run($saga);

        $this->assertFalse($generator->valid(), 'Process was not started.');
    }

    public function testHandleEffect()
    {
        $processor = new Processor();
        $effectHandler = $this->createEffectHandler();

        $processor->addEffectHandler($effectHandler);

        $effect = $this->createMock(Effect::class);

        $effect->expects($this->once())
            ->method('getName')
            ->willReturn(self::EFFECT_NAME);

        $process = $this->createMock(ProcessInterface::class);

        $effectHandler->expects($this->once())
            ->method(self::EFFECT_HANDLER_METHOD)
            ->with($this->identicalTo($effect), $this->identicalTo($process));

        $processor->handle($effect, $process);
    }

    public function testCreateSaga()
    {
        $saga = $this->createMock(SagaInterface::class);
        $this->assertEquals($saga, Processor::createSaga($saga));

        $this->assertInstanceOf(SimpleSaga::class, Processor::createSaga(function() {}));

        $this->expectException(SagaException::class);
        Processor::createSaga('invalid_saga');
    }
}