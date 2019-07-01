<?php

namespace Swis\JsonApi\Client\Interfaces;

use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;

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

    /**
     * @param \Swis\JsonApi\Client\Links|null $links
     */
    public function setLinks(? Links $links);

    /**
     * @return \Swis\JsonApi\Client\Links|null
     */
    public function getLinks(): ? Links;

    /**
     * @param \Swis\JsonApi\Client\Meta|null $meta
     */
    public function setMeta(? Meta $meta);

    /**
     * @return \Swis\JsonApi\Client\Meta|null
     */
    public function getMeta(): ? Meta;
}
