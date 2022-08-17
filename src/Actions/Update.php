<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Actions;

use Swis\JsonApi\Client\Interfaces\ItemInterface;

trait Update
{
    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     * @param array                                         $parameters
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function update(ItemInterface $item, array $parameters = [])
    {
        return $this->getClient()->patch(
            $this->getEndpoint().'/'.urlencode($item->getId()).'?'.http_build_query($parameters),
            $this->documentFactory->make($item)
        );
    }
}
