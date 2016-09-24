<?php

namespace Zack\Saga\Process;

final class Task
{
    /**
     * @var ProcessInterface
     */
    private $process;

    /**
     * Task constructor.
     * @param ProcessInterface $process
     */
    public function __construct(ProcessInterface $process)
    {
        $this->process = $process;
    }

    public function cancel()
    {
        $this->process->cancel();
    }

    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        return $this->process->isRunning();
    }

    /**
     * @return mixed
     */
    public function getReturn()
    {
        return $this->process->getReturn();
    }
}