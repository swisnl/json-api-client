<?php

namespace Swis\JsonApi\Client\Interfaces;

interface OneRelationInterface
{
    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $included
     *
     * @return static
     */
    public function associate(ItemInterface $included);

    /**
     * @return static
     */
    public function dissociate();

    /**
     * @return bool
     */
    public function hasIncluded(): bool;

    /**
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface|null
     */
    public function getIncluded();

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
