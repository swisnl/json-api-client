<?php

namespace Swis\JsonApi\Client\JsonApi;

use Art4\JsonApiClient\ElementInterface;
use Art4\JsonApiClient\ResourceCollectionInterface;
use Art4\JsonApiClient\ResourceIdentifierCollectionInterface;
use Art4\JsonApiClient\ResourceIdentifierInterface;
use Art4\JsonApiClient\ResourceItemInterface;
use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Interfaces\TypeMapperInterface;
use Swis\JsonApi\Client\Items\JenssegersItem;

class Hydrator
{
    /**
     * @var \Swis\JsonApi\Client\Interfaces\TypeMapperInterface
     */
    protected $typeMapper;

    /**
     * @param \Swis\JsonApi\Client\Interfaces\TypeMapperInterface $typeMapper
     */
    public function __construct(TypeMapperInterface $typeMapper)
    {
        $this->typeMapper = $typeMapper;
    }

    /**
     * @param \Art4\JsonApiClient\ResourceItemInterface $jsonApiItem
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface
     */
    public function hydrateItem(ResourceItemInterface $jsonApiItem): ItemInterface
    {
        $item = $this->getItemClass($jsonApiItem->get('type'));

        $item->setId($jsonApiItem->get('id'));

        if ($jsonApiItem->has('attributes')) {
            $item->fill($jsonApiItem->get('attributes')->asArray(true));
        }

        return $item;
    }

    /**
     * @param \Art4\JsonApiClient\ResourceCollectionInterface $jsonApiCollection
     *
     * @return \Swis\JsonApi\Client\Collection
     */
    public function hydrateCollection(ResourceCollectionInterface $jsonApiCollection): Collection
    {
        $collection = new Collection();
        foreach ($jsonApiCollection->asArray() as $item) {
            $collection->push($this->hydrateItem($item));
        }

        return $collection;
    }

    /**
     * @param \Swis\JsonApi\Client\Collection $jsonApiItems
     * @param \Swis\JsonApi\Client\Collection $items
     */
    public function hydrateRelationships(Collection $jsonApiItems, Collection $items)
    {
        $keyedItems = $items->reverse()->keyBy(
            function (ItemInterface $item) {
                return $this->getItemKey($item);
            }
        );

        $jsonApiItems->each(
            function (ResourceItemInterface $jsonApiItem) use ($keyedItems) {
                if (!$jsonApiItem->has('relationships')) {
                    return;
                }

                $item = $this->getItem($keyedItems, $jsonApiItem);

                if ($item === null) {
                    return;
                }

                foreach ($jsonApiItem->get('relationships')->asArray() as $name => $relationship) {
                    /** @var \Art4\JsonApiClient\ElementInterface $data */
                    $data = $relationship->get('data');
                    $method = camel_case($name);

                    if ($data instanceof ResourceIdentifierInterface) {
                        $includedItem = $this->getItem($keyedItems, $data);

                        if ($includedItem === null) {
                            continue;
                        }

                        $item->setRelation($method, $includedItem);
                    } elseif ($data instanceof ResourceIdentifierCollectionInterface) {
                        $collection = $this->getCollection($keyedItems, $data);

                        $item->setRelation($method, $collection);
                    }
                }
            }
        );
    }

    /**
     * @param string $type
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface
     */
    protected function getItemClass(string $type): ItemInterface
    {
        if ($this->typeMapper->hasMapping($type)) {
            return $this->typeMapper->getMapping($type);
        }

        return (new JenssegersItem())->setType($type);
    }

    /**
     * @param \Swis\JsonApi\Client\Collection                                                           $included
     * @param \Art4\JsonApiClient\ResourceIdentifierInterface|\Art4\JsonApiClient\ResourceItemInterface $identifier
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface|null
     */
    protected function getItem(Collection $included, $identifier)
    {
        return $included->get($this->getElementKey($identifier));
    }

    /**
     * @param \Swis\JsonApi\Client\Collection                                                                           $included
     * @param \Art4\JsonApiClient\ResourceIdentifierCollectionInterface|\Art4\JsonApiClient\ResourceCollectionInterface $identifierCollection
     *
     * @return \Swis\JsonApi\Client\Collection
     */
    protected function getCollection(Collection $included, $identifierCollection): Collection
    {
        $items = new Collection();

        foreach ($identifierCollection->asArray() as $identifier) {
            $item = $this->getItem($included, $identifier);

            if ($item === null) {
                continue;
            }

            $items->push($item);
        }

        return $items;
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     *
     * @return string
     */
    protected function getItemKey(ItemInterface $item): string
    {
        return sprintf('%s:%s', $item->getType(), $item->getId());
    }

    /**
     * @param \Art4\JsonApiClient\ElementInterface $accessor
     *
     * @return string
     */
    protected function getElementKey(ElementInterface $accessor): string
    {
        return sprintf('%s:%s', $accessor->get('type'), $accessor->get('id'));
    }
}
