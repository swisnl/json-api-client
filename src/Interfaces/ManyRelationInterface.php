<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;

/**
 * @template TItem of \Swis\JsonApi\Client\Interfaces\ItemInterface
 */
interface ManyRelationInterface
{
    /**
     * @param  \Swis\JsonApi\Client\Collection<int, TItem>|null  $data
     * @return static
     */
    public function setData(?Collection $data);

    /**
     * @return \Swis\JsonApi\Client\Collection<int, TItem>|null
     */
    public function getData(): ?Collection;

    public function hasData(): bool;

    public function setIncluded(Collection $included);

    public function getIncluded(): Collection;

    public function hasIncluded(): bool;

    /**
     * @param  \Swis\JsonApi\Client\Collection<int, TItem>  $included
     * @return static
     */
    public function associate(Collection $included);

    /**
     * @return static
     */
    public function dissociate();

    public function getAssociated(): Collection;

    public function hasAssociated(): bool;

    /**
     * @return static
     */
    public function setOmitIncluded(bool $omitIncluded);

    public function shouldOmitIncluded(): bool;

    public function setLinks(?Links $links);

    public function getLinks(): ?Links;

    public function setMeta(?Meta $meta);

    public function getMeta(): ?Meta;
}
