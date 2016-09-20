<?php

namespace Zack\Saga\Effect;

abstract class Effect
{
    /**
     * @return string
     */
    abstract public function getName(): string;
}