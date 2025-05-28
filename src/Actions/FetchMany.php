<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Actions;

/**
 * @template TItem of \Swis\JsonApi\Client\Interfaces\ItemInterface
 */
trait FetchMany
{
    /**
     * @return \Swis\JsonApi\Client\Interfaces\CollectionDocumentInterface<TItem>
     */
    public function all(array $parameters = [], array $headers = [])
    {
        return $this->getClient()->get($this->getEndpoint().'?'.http_build_query($parameters), $headers);
    }
}
