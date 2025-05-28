<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Actions;

/**
 * @template TItem of \Swis\JsonApi\Client\Interfaces\ItemInterface
 */
trait TakeOne
{
    /**
     * @return \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface<TItem>
     */
    public function take(array $parameters = [], array $headers = [])
    {
        return $this->getClient()->get($this->getEndpoint().'?'.http_build_query($parameters), $headers);
    }
}
