<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Actions;

use Swis\JsonApi\Client\Interfaces\ItemInterface;

trait Create
{
    /**
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function create(ItemInterface $item, array $parameters = [], array $headers = [])
    {
        return $this->getClient()->post(
            $this->getEndpoint().'?'.http_build_query($parameters),
            $this->documentFactory->make($item),
            $headers,
        );
    }
}
