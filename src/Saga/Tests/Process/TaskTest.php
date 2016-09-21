<?php

namespace Zack\Saga\Tests\Process;

use PHPUnit\Framework\TestCase;
use Zack\Saga\Process\ProcessInterface;
use Zack\Saga\Process\Task;

class TaskTest extends TestCase
{
    public function testTask()
    {
        $process = $this->createMock(ProcessInterface::class);
        $task = new Task($process);

        $process->expects($this->once())
            ->method('cancel');

        $task->cancel();
    }
}