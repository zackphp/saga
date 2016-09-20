<?php

namespace Zack\Saga;

use Zack\Saga\Effect\Effect;
use Zack\Saga\Process\ProcessInterface;

interface ProcessorInterface
{
    public function run($saga);

    public function handle(Effect $effect, ProcessInterface $process);
}