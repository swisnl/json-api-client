<?php

namespace Swis\JsonApi\Client\Relations;

use Swis\JsonApi\Client\Interfaces\DataInterface;
use Swis\JsonApi\Client\Interfaces\RelationInterface;
use Swis\JsonApi\Client\Items\JenssegersItem;

class MorphToRelation implements RelationInterface
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
     * @param \Swis\JsonApi\Client\Items\JenssegersItem $item
     */
    public function __construct(JenssegersItem $item)
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
        if (!$included instanceof JenssegersItem) {
            throw new \InvalidArgumentException('MorphTo expects relation to be a JenssegersItem');
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
