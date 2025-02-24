<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;

interface OneRelationInterface
{
    public function setData(?ItemInterface $data);

    public function getData(): ?ItemInterface;

    public function hasData(): bool;

    public function setIncluded(?ItemInterface $included);

    public function getIncluded(): ?ItemInterface;

    public function hasIncluded(): bool;

    /**
     * @return static
     */
    public function associate(ItemInterface $included);

    /**
     * @return static
     */
    public function dissociate();

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
