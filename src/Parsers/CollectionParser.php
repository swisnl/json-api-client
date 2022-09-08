<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Parsers;

use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Exceptions\ValidationException;

/**
 * @internal
 */
class CollectionParser
{
    private ItemParser $itemParser;

    public function __construct(ItemParser $itemParser)
    {
        $this->itemParser = $itemParser;
    }

    /**
     * @param mixed $data
     *
     * @return \Swis\JsonApi\Client\Collection
     */
    public function parse($data): Collection
    {
        if (!is_array($data)) {
            throw new ValidationException(sprintf('ResourceCollection MUST be an array, "%s" given.', gettype($data)));
        }

        return Collection::make($data)
            ->map(fn ($item) => $this->itemParser->parse($item));
    }
}
