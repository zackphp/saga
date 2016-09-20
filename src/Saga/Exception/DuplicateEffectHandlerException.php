<?php

namespace Zack\Saga\Exception;

class DuplicateEffectHandlerException extends \InvalidArgumentException
{

    /**
     * DuplicateEffectHandlerException constructor.
     * @param string $effectName
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct(string $effectName, int $code = 0, \Exception $previous = null)
    {
        $message = sprintf('Cannot register a second effect handler for "%s"', $effectName);

        parent::__construct($message, $code, $previous);
    }
}