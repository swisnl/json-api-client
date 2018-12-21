<?php

namespace Swis\JsonApi\Client\Relations;

use Swis\JsonApi\Client\Interfaces\ItemInterface;

class MorphToRelation extends AbstractOneRelation
{
    /**
     * @var \Swis\JsonApi\Client\Interfaces\ItemInterface
     */
    protected $parentItem;

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     */
    public function __construct(ItemInterface $item)
    {
        $this->parentItem = $item;
    }
}
