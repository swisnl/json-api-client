<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Actions;

use Swis\JsonApi\Client\Interfaces\ItemDocumentInterface;

/**
 * @template TItem of \Swis\JsonApi\Client\Interfaces\ItemInterface
 */
trait FetchOne
{
    /**
     * @return ItemDocumentInterface<TItem>
     */
    public function find(string $id, array $parameters = [], array $headers = [])
    {
        return $this->getClient()->get($this->getEndpoint().'/'.urlencode($id).'?'.http_build_query($parameters), $headers);
    }
}
