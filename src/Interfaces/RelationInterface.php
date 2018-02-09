<?php

namespace Swis\JsonApi\Client\Interfaces;

interface RelationInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $type
     *
     * @return static
     */
    public function setType(string $type);

    /**
     * @param \Swis\JsonApi\Client\Interfaces\DataInterface $included
     *
     * @return static
     */
    public function associate(DataInterface $included);

    /**
     * @return static
     */
    public function dissociate();

    /**
     * @return null|\Swis\JsonApi\Client\Collection|\Swis\JsonApi\Client\Interfaces\DataInterface|\Swis\JsonApi\Client\Interfaces\ItemInterface
     */
    public function getIncluded();

    /**
     * @return bool
     */
    public function hasIncluded(): bool;

    /**
     * @return bool
     */
    public function shouldOmitIncluded(): bool;

    /**
     * @param bool $omitIncluded
     *
     * @return static
     */
    public function setOmitIncluded(bool $omitIncluded);
}
