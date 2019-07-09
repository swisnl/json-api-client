<?php

namespace Swis\JsonApi\Client\Parsers;

use Art4\JsonApiClient\ResourceCollectionInterface;
use Art4\JsonApiClient\ResourceItemInterface;
use Swis\JsonApi\Client\Collection;

/**
 * @internal
 */
class CollectionParser
{
    /**
     * @var \Swis\JsonApi\Client\Parsers\ItemParser
     */
    private $itemParser;

    /**
     * @param \Swis\JsonApi\Client\Parsers\ItemParser $itemParser
     */
    public function __construct(ItemParser $itemParser)
    {
        $this->itemParser = $itemParser;
    }

    /**
     * @param \Art4\JsonApiClient\ResourceCollectionInterface $jsonApiCollection
     *
     * @return \Swis\JsonApi\Client\Collection
     */
    public function parse(ResourceCollectionInterface $jsonApiCollection): Collection
    {
        return Collection::make($jsonApiCollection->asArray())
            ->map(
                function (ResourceItemInterface $item) {
                    return $this->itemParser->parse($item);
                }
            );
    }
}
