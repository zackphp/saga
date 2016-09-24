<?php

namespace Zack\Saga\Tests\Process;

use PHPUnit\Framework\TestCase;
use Zack\Saga\Process\ProcessInterface;
use Zack\Saga\Process\Task;

class TaskTest extends TestCase
{
    const RETURN = 'return';

    public function testProxy()
    {
        $process = $this->createMock(ProcessInterface::class);
        $task = new Task($process);

        $process->expects($this->once())
            ->method('cancel');

        $task->cancel();

        $process->expects($this->once())
            ->method('isRunning')
            ->willReturn(true);

        $this->assertTrue($task->isRunning());

        $process->expects($this->once())
            ->method('getReturn')
            ->willReturn(self::RETURN);

        $this->assertSame($task->getReturn(), self::RETURN);
    }
}