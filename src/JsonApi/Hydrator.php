<?php

namespace Swis\JsonApi\JsonApi;

use Art4\JsonApiClient\AccessInterface;
use Art4\JsonApiClient\Resource\CollectionInterface as JsonApiCollection;
use Art4\JsonApiClient\Resource\IdentifierCollection;
use Art4\JsonApiClient\Resource\ItemInterface as JsonApItem;
use Swis\JsonApi\Collection;
use Swis\JsonApi\Interfaces\ItemInterface;
use Swis\JsonApi\Interfaces\TypeMapperInterface;
use Swis\JsonApi\Items\JenssegersItem;
use Swis\JsonApi\Items\NullItem;

class Hydrator
{
    /**
     * @var \Swis\JsonApi\Interfaces\TypeMapperInterface
     */
    private $typeMapper;

    /**
     * @param \Swis\JsonApi\Interfaces\TypeMapperInterface $typeMapper
     */
    public function __construct(TypeMapperInterface $typeMapper)
    {
        $this->typeMapper = $typeMapper;
    }

    /**
     * @param \Art4\JsonApiClient\Resource\CollectionInterface $jsonApiCollection
     *
     * @return \Swis\JsonApi\Collection
     */
    public function hydrateCollection(JsonApiCollection $jsonApiCollection)
    {
        $collection = new Collection();
        foreach ($jsonApiCollection->asArray() as $item) {
            $collection->push($this->hydrateItem($item));
        }

        return $collection;
    }

    /**
     * @param \Art4\JsonApiClient\Resource\ItemInterface $jsonApiItem
     *
     * @return \Swis\JsonApi\Interfaces\ItemInterface
     */
    public function hydrateItem(JsonApItem $jsonApiItem)
    {
        $item = $this->getItemClass($jsonApiItem);

        $item->setType($jsonApiItem->get('type'))
            ->setId($jsonApiItem->get('id'));

        $this->hydrateAttributes($jsonApiItem, $item);

        return $item;
    }

    /**
     * @param \Art4\JsonApiClient\Resource\ItemInterface $jsonApiItem
     *
     * @return \Swis\JsonApi\Interfaces\ItemInterface
     */
    protected function getItemClass(JsonApItem $jsonApiItem): ItemInterface
    {
        $type = $jsonApiItem->get('type');
        if ($this->typeMapper->hasMapping($type)) {
            return $this->typeMapper->getMapping($type);
        }

        return new JenssegersItem();
    }

    /**
     * @param \Art4\JsonApiClient\Resource\ItemInterface $jsonApiItem
     * @param \Swis\JsonApi\Interfaces\ItemInterface     $item
     */
    protected function hydrateAttributes(JsonApItem $jsonApiItem, ItemInterface $item)
    {
        if ($jsonApiItem->has('attributes')) {
            $item->fill($jsonApiItem->get('attributes')->asArray(true));
        }
    }

    /**
     * @param \Swis\JsonApi\Collection $jsonApiItems
     * @param \Swis\JsonApi\Collection $items
     */
    public function hydrateRelationships(Collection $jsonApiItems, Collection $items)
    {
        $jsonApiItems->each(
            function (JsonApItem $jsonApiItem) use ($items) {
                if (!$jsonApiItem->has('relationships')) {
                    return;
                }

                $item = $this->getIncludedItem($items, $jsonApiItem);

                if ($item instanceof NullItem) {
                    return;
                }

                $relationships = $this->getJsonApiDocumentRelationships($jsonApiItem);

                foreach ($relationships as $name => $relationship) {
                    /** @var \Art4\JsonApiClient\Resource\ResourceInterface $data */
                    $data = $relationship->get('data');
                    $method = camel_case($name);

                    if ($data->isIdentifier()) {
                        $includedItem = $this->getIncludedItem($items, $data);

                        if ($includedItem instanceof NullItem) {
                            continue;
                        }

                        $item->setRelation($method, $includedItem);
                    } elseif ($data->isCollection()) {
                        $collection = $this->getIncludedItems($items, $data);

                        $item->setRelation($method, $collection);
                    }
                }
            }
        );
    }

    /**
     * @param \Art4\JsonApiClient\Resource\ItemInterface $jsonApiItem
     *
     * @return \Art4\JsonApiClient\Relationship[]
     */
    protected function getJsonApiDocumentRelationships(JsonApItem $jsonApiItem): array
    {
        return $jsonApiItem->get('relationships')->asArray(false);
    }

    /**
     * @param \Swis\JsonApi\Collection            $included
     * @param \Art4\JsonApiClient\AccessInterface $accessor
     *
     * @return \Swis\JsonApi\Interfaces\ItemInterface
     */
    protected function getIncludedItem(Collection $included, AccessInterface $accessor): ItemInterface
    {
        return $included->first(
            function (ItemInterface $item) use ($accessor) {
                return $this->accessorBelongsToItem($accessor, $item);
            },
            new NullItem()
        );
    }

    /**
     * @param \Art4\JsonApiClient\AccessInterface    $accessor
     * @param \Swis\JsonApi\Interfaces\ItemInterface $item
     *
     * @return bool
     */
    protected function accessorBelongsToItem(AccessInterface $accessor, ItemInterface $item): bool
    {
        return $item->getType() === $accessor->get('type')
            && (string)$item->getId() === $accessor->get('id');
    }

    /**
     * @param \Swis\JsonApi\Collection                          $included
     * @param \Art4\JsonApiClient\Resource\IdentifierCollection $collection
     *
     * @return \Swis\JsonApi\Collection
     */
    protected function getIncludedItems(Collection $included, IdentifierCollection $collection): Collection
    {
        return $included->filter(
            function (ItemInterface $item) use ($collection) {
                return $this->itemExistsInRelatedIdentifiers($collection->asArray(false), $item);
            }
        )->values();
    }

    /**
     * @param \Art4\JsonApiClient\Resource\Identifier[] $relatedIdentifiers
     * @param \Swis\JsonApi\Interfaces\ItemInterface    $item
     *
     * @return bool
     */
    protected function itemExistsInRelatedIdentifiers(array $relatedIdentifiers, ItemInterface $item): bool
    {
        foreach ($relatedIdentifiers as $relatedIdentifier) {
            if ($this->accessorBelongsToItem($relatedIdentifier, $item)) {
                return true;
            }
        }

        return false;
    }
}
