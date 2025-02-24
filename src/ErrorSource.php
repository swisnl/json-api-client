<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client;

class ErrorSource
{
    /**
     * @var string|null
     */
    protected $pointer;

    /**
     * @var string|null
     */
    protected $parameter;

    public function __construct(?string $pointer = null, ?string $parameter = null)
    {
        $this->pointer = $pointer;
        $this->parameter = $parameter;
    }

    /**
     * @return string|null
     */
    public function getPointer()
    {
        return $this->pointer;
    }

    /**
     * @return string|null
     */
    public function getParameter()
    {
        return $this->parameter;
    }
}
