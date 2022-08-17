<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Actions;

trait FetchOne
{
    /**
     * @param string $id
     * @param array  $parameters
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function find(string $id, array $parameters = [])
    {
        return $this->getClient()->get($this->getEndpoint().'/'.urlencode($id).'?'.http_build_query($parameters));
    }
}
