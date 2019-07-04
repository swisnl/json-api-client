<?php

namespace Swis\JsonApi\Client;

use Swis\JsonApi\Client\Interfaces\ItemInterface;

class ItemDocumentBuilder
{
    /**
     * @var \Swis\JsonApi\Client\ItemHydrator
     */
    private $itemHydrator;

    /**
     * @param \Swis\JsonApi\Client\ItemHydrator $itemHydrator
     */
    public function __construct(ItemHydrator $itemHydrator)
    {
        $this->itemHydrator = $itemHydrator;
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     * @param array                                         $attributes
     * @param string|null                                   $id
     *
     * @return \Swis\JsonApi\Client\ItemDocument
     */
    public function build(ItemInterface $item, array $attributes, string $id = null)
    {
        $this->itemHydrator->hydrate($item, $attributes, $id);

        return (new ItemDocument())->setData($item)->setIncluded($item->getIncluded());
    }
}
