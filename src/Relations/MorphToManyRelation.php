<?php

namespace Swis\JsonApi\Relations;

use Swis\JsonApi\Collection;
use Swis\JsonApi\Interfaces\DataInterface;
use Swis\JsonApi\Interfaces\RelationInterface;

class MorphToManyRelation implements RelationInterface
{
    /** @var \Swis\JsonApi\Collection */
    protected $included;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $omitIncluded = false;


    public function getType(): string
    {
        throw new \LogicException('Type is not set in a MorphToMany-relationships');
    }

    /**
     * Sort the included collection by the given key.
     * You can also pass your own callback to determine how to sort the collection values.
     *
     * @param callable $callback
     * @param int      $options
     * @param bool     $descending
     *
     * @return self
     */
    public function sortBy($callback, $options = SORT_REGULAR, $descending = false)
    {
        // Included may be empty when defining the relation (on the item),
        // but will be filled when using the relation to fetch the data.
        // Checking if we have included items and applying the order is
        // simpler then keeping track of the sorts and applying them later.
        if ($this->hasIncluded()) {
            $this->included = $this->getIncluded()->sortBy($callback, $options, $descending);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasIncluded(): bool
    {
        return !$this->getIncluded()->isEmpty();
    }

    /**
     * @return \Swis\JsonApi\Collection
     */
    public function getIncluded(): Collection
    {
        return $this->included ?: new Collection();
    }

    /**
     * @param \Swis\JsonApi\Interfaces\DataInterface $included
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public function associate(DataInterface $included)
    {
        if (!$included instanceof Collection) {
            throw new \InvalidArgumentException('HasMany expects relation to be a collection');
        }

        $this->included = $included;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return static
     * @throws \LogicException
     */
    public function setType(string $type)
    {
        throw new \LogicException('Type is not set in a MorphToMany-relationships');
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
