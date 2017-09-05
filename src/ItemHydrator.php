<?php

namespace Swis\JsonApi;

use Swis\JsonApi\Interfaces\ItemInterface;
use Swis\JsonApi\Interfaces\RelationInterface;
use Swis\JsonApi\Interfaces\TypeMapperInterface;
use Swis\JsonApi\Items\JenssegersItem;
use Swis\JsonApi\Relations\HasManyRelation;
use Swis\JsonApi\Relations\HasOneRelation;

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
     * @param \Swis\JsonApi\Interfaces\RelationInterface $relation
     * @param array                                      $relationData
     *
     * @throws \InvalidArgumentException
     *
     * @return \Swis\JsonApi\Items\JenssegersItem
     */
    protected function buildRelationItem(RelationInterface $relation, array $relationData): JenssegersItem
    {
        if ($this->typeMapper->hasMapping($relation->getType())) {
            $relationItem = $this->typeMapper->getMapping($relation->getType());
        } else {
            $relationItem = new JenssegersItem();
            $relationItem->setType($relation->getType());
        }

        $relationItem->fill($relationData);
        $relationItem->setId($relationData['id']);

        return $relationItem;
    }
}
