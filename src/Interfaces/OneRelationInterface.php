<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;

interface OneRelationInterface
{
    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface|null $data
     */
    public function setData(?ItemInterface $data);

    /**
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface|null
     */
    public function getData(): ?ItemInterface;

    /**
     * @return bool
     */
    public function hasData(): bool;

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface|null $included
     */
    public function setIncluded(?ItemInterface $included);

    /**
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface|null
     */
    public function getIncluded(): ?ItemInterface;

    /**
     * @return bool
     */
    public function hasIncluded(): bool;

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
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface|null
     */
    public function getAssociated(): ?ItemInterface;

    /**
     * @return bool
     */
    public function hasAssociated(): bool;

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
