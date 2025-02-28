<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Actions;

trait TakeOne
{
    /**
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function take(array $parameters = [], array $headers = [])
    {
        return $this->getClient()->get($this->getEndpoint().'?'.http_build_query($parameters), $headers);
    }
}
