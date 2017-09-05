<?php

namespace Swis\JsonApi;

class CollectionDocumentBuilder
{
    /**
     * @param \Swis\JsonApi\Interfaces\ItemInterface[] $items
     *
     * @return \Swis\JsonApi\CollectionDocument
     */
    public function build(array $items)
    {
        return (new CollectionDocument())->setData(new Collection($items));
    }
}
