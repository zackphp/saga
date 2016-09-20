<?php

namespace Zack\Saga\Exception;

class UnknownEffectException extends \InvalidArgumentException
{
    /**
     * UnknownEffectException constructor.
     * @param string $effectName
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct(string $effectName, int $code = 0, \Exception $previous = null)
    {
        $message = sprintf('Unknown effect "%s".', $effectName);

        parent::__construct($message, $code, $previous);
    }
}