<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;

/**
 * @template TItem of \Swis\JsonApi\Client\Interfaces\ItemInterface
 */
interface OneRelationInterface
{
    /**
     * @param  TItem|null  $data
     * @return static
     */
    public function setData(?ItemInterface $data);

    /**
     * @return TItem|null
     */
    public function getData(): ?ItemInterface;

    public function hasData(): bool;

    /**
     * @param  TItem|null  $included
     */
    public function setIncluded(?ItemInterface $included);

    /**
     * @return TItem|null
     */
    public function getIncluded(): ?ItemInterface;

    public function hasIncluded(): bool;

    /**
     * @param  TItem  $included
     * @return static
     */
    public function associate(ItemInterface $included);

    /**
     * @return static
     */
    public function dissociate();

    /**
     * @return TItem|null
     */
    public function getAssociated(): ?ItemInterface;

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
