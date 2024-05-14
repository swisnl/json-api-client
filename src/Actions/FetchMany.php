<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Actions;

trait FetchMany
{
    /**
     * @param array $parameters
     * @param array $headers
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function all(array $parameters = [], array $headers = [])
    {
        return $this->getClient()->get($this->getEndpoint().'?'.http_build_query($parameters), $headers);
    }
}
