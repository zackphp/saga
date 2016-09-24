<?php

namespace Zack\Saga\Process;

interface ProcessInterface
{
    public function start();

    public function current();

    public function next();

    public function send($value);

    public function cancel();

    public function isRunning();

    public function getReturn();
}