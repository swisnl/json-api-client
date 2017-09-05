<?php

namespace Swis\JsonApi\Interfaces;

interface RelationInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type);

    /**
     * @param \Swis\JsonApi\Interfaces\DataInterface $included
     *
     * @return $this
     */
    public function associate(DataInterface $included);

    /**
     * @return $this
     */
    public function dissociate();

    /**
     * @return null|\Swis\JsonApi\Collection|\Swis\JsonApi\Interfaces\DataInterface|\Swis\JsonApi\Interfaces\ItemInterface
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
