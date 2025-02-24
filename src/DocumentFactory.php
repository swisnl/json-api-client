<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client;

use Swis\JsonApi\Client\Exceptions\UnsupportedDataException;
use Swis\JsonApi\Client\Interfaces\DataInterface;
use Swis\JsonApi\Client\Interfaces\DocumentInterface;
use Swis\JsonApi\Client\Interfaces\ItemInterface;

class DocumentFactory
{
    /**
     * @return \Swis\JsonApi\Client\ItemDocument|\Swis\JsonApi\Client\CollectionDocument
     */
    public function make(DataInterface $data): DocumentInterface
    {
        if ($data instanceof ItemInterface) {
            $document = new ItemDocument;
        } elseif ($data instanceof Collection) {
            $document = new CollectionDocument;
        } else {
            throw new UnsupportedDataException(sprintf('%s is not supported as input', get_class($data)));
        }

        return $document->setData($data)->setIncluded($this->getIncluded($data));
    }

    private function getIncluded(DataInterface $data): Collection
    {
        return Collection::wrap($data)
            ->flatMap(fn (ItemInterface $item) => $this->getIncludedFromItem($item))
            ->unique(static fn (ItemInterface $item) => sprintf('%s:%s', $item->getType(), $item->getId()))
            ->values();
    }

    private function getIncludedFromItem(ItemInterface $item): Collection
    {
        return Collection::make($item->getRelations())
            ->reject(
                static function ($relationship) {
                    /* @var \Swis\JsonApi\Client\Interfaces\OneRelationInterface|\Swis\JsonApi\Client\Interfaces\ManyRelationInterface $relationship */
                    return $relationship->shouldOmitIncluded() || ! $relationship->hasIncluded();
                }
            )
            ->flatMap(
                static function ($relationship) {
                    /* @var \Swis\JsonApi\Client\Interfaces\OneRelationInterface|\Swis\JsonApi\Client\Interfaces\ManyRelationInterface $relationship */
                    return Collection::wrap($relationship->getIncluded());
                }
            )
            ->flatMap(fn (ItemInterface $item) => Collection::wrap($item)->merge($this->getIncludedFromItem($item)))
            ->filter(fn (ItemInterface $item) => $this->itemCanBeIncluded($item));
    }

    private function itemCanBeIncluded(ItemInterface $item): bool
    {
        return $item->hasType()
            && $item->hasId()
            && ($item->hasAttributes() || $item->hasRelationships());
    }
}
