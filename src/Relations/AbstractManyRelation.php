<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Relations;

use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Interfaces\ManyRelationInterface;

/**
 * @property \Swis\JsonApi\Client\Collection|false|null $data
 * @property \Swis\JsonApi\Client\Collection|false|null $included
 */
abstract class AbstractManyRelation extends AbstractRelation implements ManyRelationInterface
{
    /**
     * @param \Swis\JsonApi\Client\Collection|null $data
     *
     * @return $this
     */
    public function setData(?Collection $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return \Swis\JsonApi\Client\Collection|null
     */
    public function getData(): ?Collection
    {
        return $this->data ?: null;
    }

    /**
     * @param \Swis\JsonApi\Client\Collection $included
     *
     * @return $this
     */
    public function setIncluded(Collection $included)
    {
        $this->included = $included;

        return $this;
    }

    /**
     * @return \Swis\JsonApi\Client\Collection
     */
    public function getIncluded(): Collection
    {
        return $this->included ?: new Collection();
    }

    /**
     * @param \Swis\JsonApi\Client\Collection $included
     *
     * @return $this
     */
    public function associate(Collection $included)
    {
        return $this->setData($included)
            ->setIncluded($included);
    }

    /**
     * @return \Swis\JsonApi\Client\Collection
     */
    public function getAssociated(): Collection
    {
        if ($this->hasIncluded()) {
            return $this->getIncluded();
        }

        if ($this->hasData()) {
            return $this->getData();
        }

        return new Collection();
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
