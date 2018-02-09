<?php

namespace Swis\JsonApi\Client\Relations;

use Swis\JsonApi\Client\Interfaces\DataInterface;
use Swis\JsonApi\Client\Interfaces\RelationInterface;
use Swis\JsonApi\Client\Items\JenssegersItem;

class HasOneRelation implements RelationInterface
{
    /**
     * @var \Swis\JsonApi\Client\Items\JenssegersItem
     */
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
     * @var \Swis\JsonApi\Client\Items\JenssegersItem
     */
    protected $parentItem;

    /**
     * @var bool
     */
    protected $omitIncluded = false;

    /**
     * @param string                                    $type
     * @param \Swis\JsonApi\Client\Items\JenssegersItem $item
     */
    public function __construct(string $type, JenssegersItem $item)
    {
        $this->parentItem = $item;
        $this->type = $type;
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\DataInterface $included
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public function associate(DataInterface $included)
    {
        if (!$included instanceof JenssegersItem) {
            throw new \InvalidArgumentException('HasOne expects relation to be a JenssegersItem');
        }

        $this->setId($included->getId());
        $this->included = $included;

        // Set the $relation.'_id' on the parent
        $this->parentItem->setAttribute($this->type.'_id', $this->getId());

        return $this;
    }

    /**
     * @return static
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
     * @return static
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return static
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
     * @return \Swis\JsonApi\Client\Items\JenssegersItem|null
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
        return null !== $this->included;
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
