<?php

namespace Swis\JsonApi\Client\Fixtures;

class MockNotFoundException extends \Exception
{
    /**
     * @var array
     */
    protected $possiblePaths;

    /**
     * @param string          $message
     * @param array           $possiblePaths
     * @param int             $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = '', array $possiblePaths = [], int $code = 0, \Throwable $previous = null)
    {
        $this->possiblePaths = $possiblePaths;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getPossiblePaths(): array
    {
        return $this->possiblePaths;
    }
}
