<?php

namespace Swis\JsonApi;

use Swis\JsonApi\Interfaces\ItemInterface;

class ItemDocumentBuilder
{
    /**
     * @var \Swis\JsonApi\ItemHydrator
     */
    private $itemHydrator;

    /**
     * ItemDocumentBuilder constructor.
     *
     * @param \Swis\JsonApi\ItemHydrator $itemHydrator
     */
    public function __construct(ItemHydrator $itemHydrator)
    {
        $this->itemHydrator = $itemHydrator;
    }

    /**
     * @param \Swis\JsonApi\Interfaces\ItemInterface $item
     * @param array                                  $attributes
     * @param int                                    $id
     *
     * @return \Swis\JsonApi\ItemDocument
     */
    public function build(ItemInterface $item, array $attributes, $id = null)
    {
        $this->itemHydrator->hydrate($item, $attributes);

        if ($id) {
            $item->setId($id);
        }

        return (new ItemDocument())->setData($item)->setIncluded($item->getIncluded());
    }
}
