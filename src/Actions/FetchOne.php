<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Actions;

trait FetchOne
{
    /**
     * @param string $id
     * @param array  $parameters
     * @param array  $headers
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function find(string $id, array $parameters = [], array $headers = [])
    {
        return $this->getClient()->get($this->getEndpoint().'/'.urlencode($id).'?'.http_build_query($parameters), $headers);
    }
}
