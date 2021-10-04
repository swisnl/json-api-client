<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Parsers;

use Swis\JsonApi\Client\Exceptions\ValidationException;
use Swis\JsonApi\Client\Meta;

/**
 * @internal
 */
class MetaParser
{
    /**
     * @param mixed $data
     *
     * @return \Swis\JsonApi\Client\Meta
     */
    public function parse($data): Meta
    {
        if (!is_object($data)) {
            throw new ValidationException(sprintf('Meta MUST be an object, "%s" given.', gettype($data)));
        }

        return new Meta((array) $data);
    }
}
