<?php

namespace Swis\JsonApi\Client\Relations;

use Swis\JsonApi\Client\Interfaces\DataInterface;
use Swis\JsonApi\Client\Interfaces\ItemInterface;

class HasOneRelation extends AbstractOneRelation
{
    /**
     * @var \Swis\JsonApi\Client\Interfaces\ItemInterface
     */
    protected $parentItem;

    /**
     * @param string                                        $type
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     */
    public function __construct(string $type, ItemInterface $item)
    {
        $this->type = $type;
        $this->parentItem = $item;
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\DataInterface $included
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function associate(DataInterface $included)
    {
        $result = parent::associate($included);

        // Set the $relation.'_id' on the parent
        $this->parentItem->setAttribute($this->type.'_id', $this->getId());

        return $result;
    }

    /**
     * @return $this
     */
    public function dissociate()
    {
        $result = parent::dissociate();

        // Remove the $relation.'_id' on the parent
        $this->parentItem->setAttribute($this->type.'_id', null);

        return $result;
    }
}
