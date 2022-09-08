<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;

interface ManyRelationInterface
{
    /**
     * @param \Swis\JsonApi\Client\Collection|null $data
     */
    public function setData(?Collection $data);

    /**
     * @return \Swis\JsonApi\Client\Collection|null
     */
    public function getData(): ?Collection;

    /**
     * @return bool
     */
    public function hasData(): bool;

    /**
     * @param \Swis\JsonApi\Client\Collection $included
     */
    public function setIncluded(Collection $included);

    /**
     * @return \Swis\JsonApi\Client\Collection
     */
    public function getIncluded(): Collection;

    /**
     * @return bool
     */
    public function hasIncluded(): bool;

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
     * @return \Swis\JsonApi\Client\Collection
     */
    public function getAssociated(): Collection;

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
