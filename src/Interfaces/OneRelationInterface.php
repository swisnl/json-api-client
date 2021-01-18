<?php

namespace Swis\JsonApi\Client\Interfaces;

use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;

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
    public function getIncluded(): ?ItemInterface;

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
    public function setLinks(?Links $links);

    /**
     * @return \Swis\JsonApi\Client\Links|null
     */
    public function getLinks(): ?Links;

    /**
     * @param \Swis\JsonApi\Client\Meta|null $meta
     */
    public function setMeta(?Meta $meta);

    /**
     * @return \Swis\JsonApi\Client\Meta|null
     */
    public function getMeta(): ?Meta;
}
