<?php

namespace Swis\JsonApi\Client\Relations;

use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Interfaces\DataInterface;

abstract class AbstractManyRelation extends AbstractRelation
{
    /**
     * @var \Swis\JsonApi\Client\Collection|null
     */
    protected $included;

    /**
     * @param \Swis\JsonApi\Client\Interfaces\DataInterface $included
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function associate(DataInterface $included)
    {
        if (!$included instanceof Collection) {
            throw new \InvalidArgumentException(
                sprintf('%s expects relation to be an instance of %s', static::class, Collection::class)
            );
        }

        $this->included = $included;

        return $this;
    }

    /**
     * @return $this
     */
    public function dissociate()
    {
        $this->included = null;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasIncluded(): bool
    {
        return $this->getIncluded()->isNotEmpty();
    }

    /**
     * @return \Swis\JsonApi\Client\Collection
     */
    public function getIncluded(): Collection
    {
        return $this->included ?: new Collection();
    }

    /**
     * Sort the included collection by the given key.
     * You can also pass your own callback to determine how to sort the collection values.
     *
     * @param callable $callback
     * @param int      $options
     * @param bool     $descending
     *
     * @return $this
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
}
