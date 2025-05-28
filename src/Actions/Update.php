<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Actions;

use Swis\JsonApi\Client\Interfaces\ItemInterface;

/**
 * @template TItem of \Swis\JsonApi\Client\Interfaces\ItemInterface
 */
trait Update
{
    /**
     * @return \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface<TItem>
     */
    public function update(ItemInterface $item, array $parameters = [], array $headers = [])
    {
        return $this->getClient()->patch(
            $this->getEndpoint().'/'.urlencode($item->getId()).'?'.http_build_query($parameters),
            $this->documentFactory->make($item),
            $headers,
        );
    }
}
