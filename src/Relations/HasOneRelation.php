<?php

namespace Swis\JsonApi\Relations;

use Swis\JsonApi\Interfaces\DataInterface;
use Swis\JsonApi\Interfaces\ItemInterface;
use Swis\JsonApi\Interfaces\RelationInterface;

class HasOneRelation implements RelationInterface
{
    /** @var ItemInterface */
    protected $included;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var \Swis\JsonApi\Items\JenssegersItem
     */
    protected $parentItem;

    /**
     * @var bool
     */
    protected $omitIncluded = false;

    /**
     * OneToOneRelation constructor.
     *
     * @param string                                 $type
     * @param \Swis\JsonApi\Interfaces\ItemInterface $item
     */
    public function __construct(string $type, ItemInterface $item)
    {
        $this->parentItem = $item;
        $this->type = $type;
    }

    /**
     * @param \Swis\JsonApi\Interfaces\DataInterface $included
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function associate(DataInterface $included)
    {
        if (!$included instanceof ItemInterface) {
            throw new \InvalidArgumentException('HasOne expects relation to be an item');
        }

        $this->setId($included->getId());
        $this->included = $included;

        // Set the $relation.'_id' on the parent
        $this->parentItem->setAttribute($this->type.'_id', $this->getId());

        return $this;
    }

    /**
     * @return $this
     */
    public function dissociate()
    {
        $this->included = null;

        // Remove the $relation.'_id' on the parent
        $this->parentItem->setAttribute($this->type.'_id', null);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return \Swis\JsonApi\Interfaces\DataInterface|null
     */
    public function getIncluded()
    {
        return $this->included;
    }

    /**
     * @return bool
     */
    public function hasIncluded(): bool
    {
        return !empty($this->included);
    }

    /**
     * @param bool $omitIncluded
     *
     * @return static
     */
    public function setOmitIncluded(bool $omitIncluded)
    {
        $this->omitIncluded = $omitIncluded;

        return $this;
    }

    /**
     * @return bool
     */
    public function shouldOmitIncluded(): bool
    {
        return $this->omitIncluded;
    }
}
