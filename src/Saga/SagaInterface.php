<?php

namespace Zack\Saga;

interface SagaInterface
{
    public function run(): \Generator;
}