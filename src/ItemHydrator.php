<?php

namespace Swis\JsonApi;

use Swis\JsonApi\Interfaces\ItemInterface;
use Swis\JsonApi\Interfaces\RelationInterface;
use Swis\JsonApi\Interfaces\TypeMapperInterface;
use Swis\JsonApi\Items\JenssegersItem;
use Swis\JsonApi\Relations\HasManyRelation;
use Swis\JsonApi\Relations\HasOneRelation;
use Swis\JsonApi\Relations\MorphToRelation;

class ItemHydrator
{
    /**
     * @var \Swis\JsonApi\TypeMapper
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
     * @param \Swis\JsonApi\Interfaces\ItemInterface $item
     * @param array                                  $attributes
     *
     * @throws \Exception
     *
     * @return \Swis\JsonApi\Interfaces\ItemInterface
     */
    public function hydrate(ItemInterface $item, array $attributes): ItemInterface
    {
        $this->fill($item, $attributes);
        $this->fillRelations($item, $attributes);

        return $item;
    }

    /**
     * @param \Swis\JsonApi\Interfaces\ItemInterface $item
     * @param array|null                             $attributes
     */
    protected function fill(ItemInterface $item, array $attributes = null)
    {
        $item->fill($attributes);
    }

    /**
     * Get relationships from the attributes and add them to the item.
     *
     * @param \Swis\JsonApi\Interfaces\ItemInterface $item
     * @param array                                  $attributes
     *
     * @throws \Exception
     */
    protected function fillRelations(ItemInterface $item, array $attributes = null)
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
                $this->hydrateHasOneRelation($item, $attributes, $relation, $availableRelation);
            } elseif ($relation instanceof HasManyRelation) {
                $this->hydrateHasManyRelation($attributes, $availableRelation, $relation);
            } elseif ($relation instanceof MorphToRelation) {
                $this->hydrateMorphToRelation($attributes, $relation, $availableRelation);
            }
        }
    }

    /**
     * @param \Swis\JsonApi\Interfaces\ItemInterface $item
     * @param string                                 $availableRelation
     *
     * @throws \Exception
     *
     * @return \Swis\JsonApi\Interfaces\RelationInterface
     */
    protected function getRelationFromItem(ItemInterface $item, string $availableRelation): RelationInterface
    {
        $method = camel_case($availableRelation);
        if (!method_exists($item, $method)) {
            throw new \Exception(sprintf('Method %s not found on %s', $method, get_class($item)));
        }

        return $item->$method();
    }

    /**
     * @param \Swis\JsonApi\Interfaces\ItemInterface $item
     * @param array                                  $attributes
     * @param \Swis\JsonApi\Relations\HasOneRelation $relation
     * @param string                                 $availableRelation
     *
     * @throws \InvalidArgumentException
     */
    protected function hydrateHasOneRelation(
        ItemInterface $item,
        array $attributes,
        HasOneRelation $relation,
        string $availableRelation
    ) {
        if (is_array($attributes[$availableRelation])) {
            $relationItem = $this->buildRelationItem($relation, $attributes[$availableRelation]);
            $relation->associate($relationItem);
        } else {
            $relation->setId($attributes[$availableRelation]);
            $item->setAttribute($availableRelation.'_id', $attributes[$availableRelation]);
        }
    }

    /**
     * @param array                                   $attributes
     * @param string                                  $availableRelation
     * @param \Swis\JsonApi\Relations\HasManyRelation $relation
     *
     * @throws \InvalidArgumentException
     */
    protected function hydrateHasManyRelation(array $attributes, string $availableRelation, HasManyRelation $relation)
    {
        foreach ($attributes[$availableRelation] as $relationData) {
            $relationItem = $this->buildRelationItem($relation, $relationData);

            $relation->associate($relation->getIncluded()->push($relationItem));
        }
    }

    /**
     * @param array                                   $attributes
     * @param \Swis\JsonApi\Relations\MorphToRelation $relation
     * @param string                                  $availableRelation
     */
    protected function hydrateMorphToRelation(
        array $attributes,
        MorphToRelation $relation,
        string $availableRelation
    ) {
        if (!array_key_exists('type', $attributes[$availableRelation])) {
            throw new \InvalidArgumentException('Always provide a "type" attribute in a morphTo relationship');
        }

        $relationItem = $this->buildRelationItem($relation, $attributes[$availableRelation], $attributes[$availableRelation]['type']);
        $relation->associate($relationItem);
    }

    /**
     * @param \Swis\JsonApi\Interfaces\RelationInterface $relation
     * @param array                                      $relationData
     * @param string|null                                $relatedType
     *
     * @return \Swis\JsonApi\Items\JenssegersItem
     */
    protected function buildRelationItem(RelationInterface $relation, array $relationData, string $relatedType = null): JenssegersItem
    {
        // Sometimes the relatedType is provided from the relationship, but not always (i.e. Polymorphic Relationships)
        if ($relatedType) {
            $type = $relatedType;
        } else {
            $type = $relation->getType();
        }

        if ($this->typeMapper->hasMapping($type)) {
            $relationItem = $this->typeMapper->getMapping($type);
        } else {
            $relationItem = new JenssegersItem();
            $relationItem->setType($type);
        }

        $relationItem->fill($relationData);
        $relationItem->setId($relationData['id']);

        return $relationItem;
    }
}
