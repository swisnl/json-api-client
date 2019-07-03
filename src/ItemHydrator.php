<?php

namespace Swis\JsonApi\Client;

use Illuminate\Support\Str;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Interfaces\TypedRelationInterface;
use Swis\JsonApi\Client\Interfaces\TypeMapperInterface;
use Swis\JsonApi\Client\Relations\HasManyRelation;
use Swis\JsonApi\Client\Relations\HasOneRelation;
use Swis\JsonApi\Client\Relations\MorphToManyRelation;
use Swis\JsonApi\Client\Relations\MorphToRelation;

class ItemHydrator
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
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     * @param array                                         $attributes
     *
     * @throws \RuntimeException
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface
     */
    public function hydrate(ItemInterface $item, array $attributes): ItemInterface
    {
        $this->fill($item, $attributes);
        $this->fillRelations($item, $attributes);

        return $item;
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     * @param array                                         $attributes
     */
    protected function fill(ItemInterface $item, array $attributes)
    {
        $item->fill(array_diff_key($attributes, array_combine($item->getAvailableRelations(), $item->getAvailableRelations())));
    }

    /**
     * Get relationships from the attributes and add them to the item.
     *
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     * @param array                                         $attributes
     *
     * @throws \RuntimeException
     */
    protected function fillRelations(ItemInterface $item, array $attributes)
    {
        // Fill Relations
        foreach ($item->getAvailableRelations() as $availableRelation) {
            if (!array_key_exists($availableRelation, $attributes)) {
                // No data found, continue
                continue;
            }

            $relation = $this->getRelationFromItem($item, $availableRelation);

            // It is a valid relation
            if ($relation instanceof HasOneRelation) {
                $this->hydrateHasOneRelation($attributes, $relation, $availableRelation);
            } elseif ($relation instanceof HasManyRelation) {
                $this->hydrateHasManyRelation($attributes, $relation, $availableRelation);
            } elseif ($relation instanceof MorphToRelation) {
                $this->hydrateMorphToRelation($attributes, $relation, $availableRelation);
            } elseif ($relation instanceof MorphToManyRelation) {
                $this->hydrateMorphToManyRelation($attributes, $relation, $availableRelation);
            }
        }
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     * @param string                                        $availableRelation
     *
     * @throws \RuntimeException
     *
     * @return \Swis\JsonApi\Client\Interfaces\OneRelationInterface|\Swis\JsonApi\Client\Interfaces\ManyRelationInterface
     */
    protected function getRelationFromItem(ItemInterface $item, string $availableRelation)
    {
        $method = Str::camel($availableRelation);
        if (!method_exists($item, $method)) {
            throw new \RuntimeException(sprintf('Method %s not found on %s', $method, get_class($item)));
        }

        return $item->$method();
    }

    /**
     * @param array                                         $attributes
     * @param \Swis\JsonApi\Client\Relations\HasOneRelation $relation
     * @param string                                        $availableRelation
     *
     * @throws \InvalidArgumentException
     */
    protected function hydrateHasOneRelation(array $attributes, HasOneRelation $relation, string $availableRelation)
    {
        if (is_array($attributes[$availableRelation])) {
            $relationItem = $this->buildRelationItem($relation, $attributes[$availableRelation]);
        } else {
            $relationItem = $this->buildRelationItem($relation, ['id' => $attributes[$availableRelation]]);
        }

        $relation->associate($relationItem);
    }

    /**
     * @param array                                          $attributes
     * @param string                                         $availableRelation
     * @param \Swis\JsonApi\Client\Relations\HasManyRelation $relation
     *
     * @throws \InvalidArgumentException
     */
    protected function hydrateHasManyRelation(array $attributes, HasManyRelation $relation, string $availableRelation)
    {
        foreach ($attributes[$availableRelation] as $relationData) {
            if (is_array($relationData)) {
                $relationItem = $this->buildRelationItem($relation, $relationData);
            } else {
                $relationItem = $this->buildRelationItem($relation, ['id' => $relationData]);
            }

            $relation->associate($relation->getIncluded()->push($relationItem));
        }
    }

    /**
     * @param array                                          $attributes
     * @param \Swis\JsonApi\Client\Relations\MorphToRelation $relation
     * @param string                                         $availableRelation
     *
     * @throws \InvalidArgumentException
     */
    protected function hydrateMorphToRelation(array $attributes, MorphToRelation $relation, string $availableRelation)
    {
        $relationData = $attributes[$availableRelation];
        if (!array_key_exists('type', $relationData)) {
            throw new \InvalidArgumentException('Always provide a "type" attribute in a morphTo relationship');
        }
        $relationItem = $this->buildRelationItem($relation, array_diff_key($relationData, ['type' => 'type']), $relationData['type']);

        $relation->associate($relationItem);
    }

    /**
     * @param array                                              $attributes
     * @param \Swis\JsonApi\Client\Relations\MorphToManyRelation $relation
     * @param string                                             $availableRelation
     *
     * @throws \InvalidArgumentException
     */
    protected function hydrateMorphToManyRelation(array $attributes, MorphToManyRelation $relation, string $availableRelation)
    {
        foreach ($attributes[$availableRelation] as $relationData) {
            if (!array_key_exists('type', $relationData)) {
                throw new \InvalidArgumentException('Always provide a "type" attribute in a morphToMany relationship entry');
            }
            $relationItem = $this->buildRelationItem($relation, array_diff_key($relationData, ['type' => 'type']), $relationData['type']);

            $relation->associate($relation->getIncluded()->push($relationItem));
        }
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\OneRelationInterface|\Swis\JsonApi\Client\Interfaces\ManyRelationInterface $relation
     * @param array                                                                                                      $relationData
     * @param string|null                                                                                                $type
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface
     */
    protected function buildRelationItem($relation, array $relationData, string $type = null): ItemInterface
    {
        if (null === $type) {
            if (!$relation instanceof TypedRelationInterface) {
                throw new \InvalidArgumentException('Param $type is required when the relation is not typed');
            }

            $type = $relation->getType();
        }

        if ($this->typeMapper->hasMapping($type)) {
            $relationItem = $this->typeMapper->getMapping($type);
        } else {
            $relationItem = new Item();
            $relationItem->setType($type);
        }

        $this->fill($relationItem, $relationData);
        $this->fillRelations($relationItem, $relationData);

        $relationItem->setId($relationData['id']);

        return $relationItem;
    }
}
