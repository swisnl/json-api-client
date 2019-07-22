<?php

namespace Swis\JsonApi\Client;

use Swis\JsonApi\Client\Exceptions\UnsupportedDataException;
use Swis\JsonApi\Client\Interfaces\DataInterface;
use Swis\JsonApi\Client\Interfaces\DocumentInterface;
use Swis\JsonApi\Client\Interfaces\ItemInterface;

class DocumentFactory
{
    /**
     * @param \Swis\JsonApi\Client\Interfaces\DataInterface $data
     *
     * @return \Swis\JsonApi\Client\ItemDocument|\Swis\JsonApi\Client\CollectionDocument
     */
    public function make(DataInterface $data): DocumentInterface
    {
        if ($data instanceof ItemInterface) {
            $document = new ItemDocument();
        } elseif ($data instanceof Collection) {
            $document = new CollectionDocument();
        } else {
            throw new UnsupportedDataException(sprintf('%s is not supported as input', get_class($data)));
        }

        return $document->setData($data)->setIncluded($this->getIncluded($data));
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\DataInterface $data
     *
     * @return \Swis\JsonApi\Client\Collection
     */
    private function getIncluded(DataInterface $data): Collection
    {
        return Collection::wrap($data)
            ->flatMap(
                function (ItemInterface $item) {
                    return $this->getIncludedFromItem($item);
                }
            )
            ->unique(
                static function (ItemInterface $item) {
                    return sprintf('%s:%s', $item->getType(), $item->getId());
                }
            )
            ->values();
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     *
     * @return \Swis\JsonApi\Client\Collection
     */
    private function getIncludedFromItem(ItemInterface $item): Collection
    {
        return Collection::make($item->getRelations())
            ->reject(
                static function ($relationship) {
                    /* @var \Swis\JsonApi\Client\Interfaces\OneRelationInterface|\Swis\JsonApi\Client\Interfaces\ManyRelationInterface $relationship */
                    return $relationship->shouldOmitIncluded() || !$relationship->hasIncluded();
                }
            )
            ->flatMap(
                static function ($relationship) {
                    /* @var \Swis\JsonApi\Client\Interfaces\OneRelationInterface|\Swis\JsonApi\Client\Interfaces\ManyRelationInterface $relationship */
                    return Collection::wrap($relationship->getIncluded());
                }
            )
            ->flatMap(
                function (ItemInterface $item) {
                    return Collection::wrap($item)->merge($this->getIncludedFromItem($item));
                }
            )
            ->filter(
                function (ItemInterface $item) {
                    return $this->itemCanBeIncluded($item);
                }
            );
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     *
     * @return bool
     */
    private function itemCanBeIncluded(ItemInterface $item): bool
    {
        return $item->hasType()
            && $item->hasId()
            && ($item->hasAttributes() || $item->hasRelationships());
    }
}
