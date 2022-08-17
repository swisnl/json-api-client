<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Actions;

trait TakeOne
{
    /**
     * @param array $parameters
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function take(array $parameters = [])
    {
        return $this->getClient()->get($this->getEndpoint().'?'.http_build_query($parameters));
    }
}
