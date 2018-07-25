<?php

namespace Swis\JsonApi\Client\Relations;

use Swis\JsonApi\Client\Interfaces\DataInterface;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Interfaces\RelationInterface;

class MorphToRelation implements RelationInterface
{
    /**
     * @var \Swis\JsonApi\Client\Interfaces\ItemInterface
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
     * @var \Swis\JsonApi\Client\Interfaces\ItemInterface
     */
    protected $parentItem;

    /**
     * @var bool
     */
    protected $omitIncluded = false;

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     */
    public function __construct(ItemInterface $item)
    {
        $this->parentItem = $item;
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
        if (!$included instanceof ItemInterface) {
            throw new \InvalidArgumentException(
                sprintf('MorphTo expects relation to be an instance of %s', ItemInterface::class)
            );
        }

        $this->setId($included->getId());
        $this->setType($included->getType());

        $this->included = $included;

        return $this;
    }

    /**
     * @return static
     */
    public function dissociate()
    {
        $this->included = null;

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
     * @return null|string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface|null
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
