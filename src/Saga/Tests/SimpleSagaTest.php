<?php

namespace Zack\Saga\Tests;

use PHPUnit\Framework\TestCase;
use Zack\Saga\Exception\SagaException;
use Zack\Saga\SimpleSaga;

class SimpleSagaTest extends TestCase
{
    const MESSAGE = 'test message';
    const RETURN = 'return value';

    public function testRun()
    {
        $test = function() {
            $message = yield;
            $this->assertSame(self::MESSAGE, $message);
            yield;
            return self::RETURN;
        };

        $saga = new SimpleSaga($test);

        $generator = $saga->run();
        $this->assertInstanceOf(\Generator::class, $saga->run());

        $generator->rewind();
        $generator->send(self::MESSAGE);
        $generator->next();

        $this->assertSame(self::RETURN, $generator->getReturn());
    }

    public function testInvalidGenerator()
    {
        $test = function() {
            return 'invalid return';
        };

        $saga = new SimpleSaga($test);
        $generator = $saga->run();

        $this->expectException(SagaException::class);

        $generator->rewind();
    }
}