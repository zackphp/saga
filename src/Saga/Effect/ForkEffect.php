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
     * @param SagaInterface $saga
     */
    public function __construct(SagaInterface $saga)
    {
        $this->saga = $saga;
    }

    /**
     * @return SagaInterface
     */
    public function getSaga(): SagaInterface
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