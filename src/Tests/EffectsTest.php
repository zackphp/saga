<?php

namespace Zack\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event;
use Zack\Saga\Effect\DispatchEffect;
use Zack\Saga\Effect\ForkEffect;
use Zack\Saga\Effect\TakeEffect;
use Zack\Saga\Effects;
use Zack\Saga\SagaInterface;

class EffectsTest extends TestCase
{
    public function testTake()
    {
        $effect = Effects::take('event_name');

        $this->assertInstanceOf(TakeEffect::class, $effect);
        $this->assertSame(Effects::TAKE, $effect->getName());
    }

    public function testDispatch()
    {
        $effect = Effects::dispatch('event_name', new Event());

        $this->assertInstanceOf(DispatchEffect::class, $effect);
        $this->assertSame(Effects::DISPATCH, $effect->getName());
    }

    public function testFork()
    {
        $saga = $this->createMock(SagaInterface::class);
        $effect = Effects::fork($saga);

        $this->assertInstanceOf(ForkEffect::class, $effect);
        $this->assertSame(Effects::FORK, $effect->getName());
    }

    public function testTakeHelper()
    {
        $this->assertInstanceOf(TakeEffect::class, take('event_name'));
    }

    public function testDispatchHelper()
    {
        $this->assertInstanceOf(DispatchEffect::class, dispatch('event_name', new Event()));
    }

    public function testForkHelper()
    {
        $saga = $this->createMock(SagaInterface::class);

        $this->assertInstanceOf(ForkEffect::class, fork($saga));
    }
}