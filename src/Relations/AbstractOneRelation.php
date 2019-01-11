<?php

namespace Swis\JsonApi\Client\Relations;

use Swis\JsonApi\Client\Interfaces\DataInterface;
use Swis\JsonApi\Client\Interfaces\ItemInterface;

abstract class AbstractOneRelation extends AbstractRelation
{
    /**
     * @var \Swis\JsonApi\Client\Interfaces\ItemInterface|null
     */
    protected $included;

    /**
     * @var int
     */
    protected $id;

    /**
     * @param \Swis\JsonApi\Client\Interfaces\DataInterface $included
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function associate(DataInterface $included)
    {
        if (!$included instanceof ItemInterface) {
            throw new \InvalidArgumentException(
                sprintf('%s expects relation to be an instance of %s', static::class, ItemInterface::class)
            );
        }

        $this->setId($included->getId());

        $this->included = $included;

        return $this;
    }

    /**
     * @return $this
     */
    public function dissociate()
    {
        $this->setId(null);

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
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
        return null !== $this->getIncluded();
    }
}
