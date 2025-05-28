<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Actions;

/**
 * @template TItem of \Swis\JsonApi\Client\Interfaces\ItemInterface
 */
trait FetchOne
{
    /**
     * @return \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface<TItem>
     */
    public function find(string $id, array $parameters = [], array $headers = [])
    {
        return $this->getClient()->get($this->getEndpoint().'/'.urlencode($id).'?'.http_build_query($parameters), $headers);
    }
}
