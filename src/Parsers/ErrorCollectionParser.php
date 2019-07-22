<?php

namespace Swis\JsonApi\Client\Parsers;

use Swis\JsonApi\Client\ErrorCollection;
use Swis\JsonApi\Client\Exceptions\ValidationException;

/**
 * @internal
 */
class ErrorCollectionParser
{
    /**
     * @var \Swis\JsonApi\Client\Parsers\ErrorParser
     */
    private $errorParser;

    /**
     * @param \Swis\JsonApi\Client\Parsers\ErrorParser $errorParser
     */
    public function __construct(ErrorParser $errorParser)
    {
        $this->errorParser = $errorParser;
    }

    /**
     * @param mixed $data
     *
     * @return \Swis\JsonApi\Client\ErrorCollection
     */
    public function parse($data): ErrorCollection
    {
        if (!is_array($data)) {
            throw new ValidationException(sprintf('ErrorCollection has to be in an array, "%s" given.', gettype($data)));
        }
        if (count($data) === 0) {
            throw new ValidationException('ErrorCollection cannot be empty and MUST have at least one Error object.');
        }

        return new ErrorCollection(
            array_map(
                function ($error) {
                    return $this->errorParser->parse($error);
                },
                $data
            )
        );
    }
}
