<?php

namespace Swis\JsonApi\Client\JsonApi;

use Art4\JsonApiClient\AccessInterface;
use Art4\JsonApiClient\ResourceCollectionInterface;
use Art4\JsonApiClient\ResourceCollectionInterface as JsonApiCollection;
use Art4\JsonApiClient\ResourceIdentifierCollection as IdentifierCollection;
use Art4\JsonApiClient\ResourceIdentifierCollectionInterface;
use Art4\JsonApiClient\ResourceIdentifierInterface;
use Art4\JsonApiClient\ResourceItemInterface as JsonApItem;
use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Interfaces\TypeMapperInterface;
use Swis\JsonApi\Client\Items\JenssegersItem;
use Swis\JsonApi\Client\Items\NullItem;

class Hydrator
{
    /**
     * @var \Swis\JsonApi\Client\Interfaces\TypeMapperInterface
     */
    private $typeMapper;

    /**
     * @param \Swis\JsonApi\Client\Interfaces\TypeMapperInterface $typeMapper
     */
    public function __construct(TypeMapperInterface $typeMapper)
    {
        $this->typeMapper = $typeMapper;
    }

    /**
     * @param \Art4\JsonApiClient\ResourceCollectionInterface $jsonApiCollection
     *
     * @return \Swis\JsonApi\Client\Collection
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
     * @param \Art4\JsonApiClient\ResourceItemInterface $jsonApiItem
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface
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
     * @param \Art4\JsonApiClient\ResourceItemInterface $jsonApiItem
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface
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
     * @param \Art4\JsonApiClient\ResourceItemInterface    $jsonApiItem
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     */
    protected function hydrateAttributes(JsonApItem $jsonApiItem, ItemInterface $item)
    {
        if ($jsonApiItem->has('attributes')) {
            $item->fill($jsonApiItem->get('attributes')->asArray(true));
        }
    }

    /**
     * @param \Swis\JsonApi\Client\Collection $jsonApiItems
     * @param \Swis\JsonApi\Client\Collection $items
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
                    /** @var \Art4\JsonApiClient\ResourceItemInterface $data */
                    $data = $relationship->get('data');
                    $method = camel_case($name);

                    if ($data instanceof ResourceIdentifierInterface) {
                        $includedItem = $this->getIncludedItem($items, $data);

                        if ($includedItem instanceof NullItem) {
                            continue;
                        }

                        $item->setRelation($method, $includedItem);
                    } elseif ($data instanceof ResourceCollectionInterface || $data instanceof ResourceIdentifierCollectionInterface) {
                        $collection = $this->getIncludedItems($items, $data);

                        $item->setRelation($method, $collection);
                    }
                }
            }
        );
    }

    /**
     * @param \Art4\JsonApiClient\ResourceItemInterface $jsonApiItem
     *
     * @return \Art4\JsonApiClient\Relationship[]
     */
    protected function getJsonApiDocumentRelationships(JsonApItem $jsonApiItem): array
    {
        return $jsonApiItem->get('relationships')->asArray(false);
    }

    /**
     * @param \Swis\JsonApi\Client\Collection     $included
     * @param \Art4\JsonApiClient\AccessInterface $accessor
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface
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
     * @param \Art4\JsonApiClient\AccessInterface           $accessor
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     *
     * @return bool
     */
    protected function accessorBelongsToItem(AccessInterface $accessor, ItemInterface $item): bool
    {
        return $item->getType() === $accessor->get('type')
            && (string)$item->getId() === $accessor->get('id');
    }

    /**
     * @param \Swis\JsonApi\Client\Collection                   $included
     * @param \Art4\JsonApiClient\ResourceIdentifierCollection $collection
     *
     * @return \Swis\JsonApi\Client\Collection
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
     * @param \Art4\JsonApiClient\ResourceIdentifier[]     $relatedIdentifiers
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
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
