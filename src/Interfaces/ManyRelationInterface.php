<?php

namespace Swis\JsonApi\Client\Interfaces;

use Swis\JsonApi\Client\Collection;

interface ManyRelationInterface
{
    /**
     * @param \Swis\JsonApi\Client\Collection $included
     *
     * @return static
     */
    public function associate(Collection $included);

    /**
     * @return static
     */
    public function dissociate();

    /**
     * @return bool
     */
    public function hasIncluded(): bool;

    /**
     * @return \Swis\JsonApi\Client\Collection
     */
    public function getIncluded(): Collection;

    /**
     * @param bool $omitIncluded
     *
     * @return static
     */
    public function setOmitIncluded(bool $omitIncluded);

    /**
     * @return bool
     */
    public function shouldOmitIncluded(): bool;
}
