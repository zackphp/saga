<?php

namespace Zack\Saga\Tests;

use PHPUnit\Framework\TestCase;
use Zack\Saga\SimpleSaga;

class SimpleSagaTest extends TestCase
{
    const SAGA_METHOD = 'saga';

    public function testRun()
    {
        $mock = $this->getMockBuilder('stdClass')
            ->setMethods([self::SAGA_METHOD])
            ->getMock();

        $mock->expects($this->once())
            ->method(self::SAGA_METHOD)
            ->willReturn(new \EmptyIterator());

        $saga = new SimpleSaga([$mock, self::SAGA_METHOD]);

        $generator = $saga->run();
        $generator->rewind();
        $generator->next();
    }
}