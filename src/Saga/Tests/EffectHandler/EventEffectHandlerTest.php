<?php

namespace Zack\Saga\Tests\EffectHandler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zack\Saga\Effect\DispatchEffect;
use Zack\Saga\Effect\TakeEffect;
use Zack\Saga\EffectHandler\EventEffectHandler;
use Zack\Saga\Effects;
use Zack\Saga\Process\ProcessInterface;

class EventEffectHandlerTest extends TestCase
{
    const EVENT_NAME = 'test_event';

    public function testGetEffectHandlers()
    {
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $effectHandler = new EventEffectHandler($dispatcher);

        $this->assertEquals($effectHandler->getEffectHandlers(), [
            Effects::TAKE => 'handleTake',
            Effects::DISPATCH => 'handleDispatch'
        ]);
    }

    public function testHandleTakeAndDispatch()
    {
        $dispatcher = new EventDispatcher();
        $effectHandler = new EventEffectHandler($dispatcher);

        $takeProcess = $this->createMock(ProcessInterface::class);
        $effect = new TakeEffect(self::EVENT_NAME);

        $effectHandler->handleTake($effect, $takeProcess);

        $takeProcessPattern = $this->createMock(ProcessInterface::class);
        $effect = new TakeEffect(self::EVENT_NAME, function(Event $event) {
            /** @noinspection PhpUndefinedMethodInspection */
            return $event->accept();
        });

        $effectHandler->handleTake($effect, $takeProcessPattern);

        $event = $this->getMockBuilder(Event::class)
            ->setMethods(['accept'])
            ->getMock();

        $event->expects($this->exactly(2))
            ->method('accept')
            ->willReturn(false, true);

        $dispatchProcess = $this->createMock(ProcessInterface::class);
        $effect = new DispatchEffect(self::EVENT_NAME, $event);

        $dispatchProcess->expects($this->exactly(3))
            ->method('next');

        $takeProcess->expects($this->once())
            ->method('send')
            ->with($this->identicalTo($event));

        $effectHandler->handleDispatch($effect, $dispatchProcess);

        $takeProcessPattern->expects($this->once())
            ->method('send')
            ->with($this->identicalTo($event));

        $effectHandler->handleDispatch($effect, $dispatchProcess);
        $effectHandler->handleDispatch($effect, $dispatchProcess);
    }
}