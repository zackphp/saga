<?php

namespace Zack\Saga;

use Zack\Saga\Effect\Effect;
use Zack\Saga\Process\ProcessInterface;
use Zack\Saga\Process\Task;

interface ProcessorInterface
{
    public function run($saga): Task;

    public function handle(Effect $effect, ProcessInterface $process);
}