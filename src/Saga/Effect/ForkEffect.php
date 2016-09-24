<?php

namespace Zack\Saga\Effect;

use Zack\Saga\Effects;
use Zack\Saga\SagaInterface;

final class ForkEffect extends Effect
{
    /** @var SagaInterface */
    private $saga;

    /**
     * ForkEffect constructor.
     * @param callable|SagaInterface $saga
     */
    public function __construct($saga)
    {
        $this->saga = $saga;
    }

    /**
     * @return callable|SagaInterface
     */
    public function getSaga()
    {
        return $this->saga;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return Effects::FORK;
    }
}