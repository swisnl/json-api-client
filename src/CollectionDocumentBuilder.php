<?php

namespace Swis\JsonApi\Client;

class CollectionDocumentBuilder
{
    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface[] $items
     *
     * @return \Swis\JsonApi\Client\CollectionDocument
     */
    public function build(array $items)
    {
        return (new CollectionDocument())->setData(new Collection($items));
    }
}
