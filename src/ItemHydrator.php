<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client;

use Swis\JsonApi\Client\Exceptions\HydrationException;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Interfaces\ManyRelationInterface;
use Swis\JsonApi\Client\Interfaces\OneRelationInterface;
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
     * @param string|null                                   $id
     *
     * @throws \Swis\JsonApi\Client\Exceptions\HydrationException
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface
     */
    public function hydrate(ItemInterface $item, array $attributes, ?string $id = null): ItemInterface
    {
        $this->fill($item, $attributes);
        $this->fillRelations($item, $attributes);

        if ($id !== null && $id !== '') {
            $item->setId($id);
        }

        return $item;
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     * @param array                                         $attributes
     */
    protected function fill(ItemInterface $item, array $attributes): void
    {
        $item->fill(Util::arrayExcept($attributes, $item->getAvailableRelations()));
    }

    /**
     * Get relationships from the attributes and add them to the item.
     *
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     * @param array                                         $attributes
     *
     * @throws \Swis\JsonApi\Client\Exceptions\HydrationException
     */
    protected function fillRelations(ItemInterface $item, array $attributes): void
    {
        // Fill Relations
        foreach ($item->getAvailableRelations() as $availableRelation) {
            if (!array_key_exists($availableRelation, $attributes)) {
                // No data found, continue
                continue;
            }

            $relation = $this->getRelationFromItem($item, $availableRelation);

            // The relation should be unset
            if (
                ($relation instanceof OneRelationInterface && $attributes[$availableRelation] === null)
                || ($relation instanceof ManyRelationInterface && $attributes[$availableRelation] === [])
            ) {
                $relation->dissociate();

                continue;
            }

            // It is a valid relation
            if ($relation instanceof HasOneRelation) {
                $this->hydrateHasOneRelation($relation, $attributes[$availableRelation]);
            } elseif ($relation instanceof HasManyRelation) {
                $this->hydrateHasManyRelation($relation, $attributes[$availableRelation]);
            } elseif ($relation instanceof MorphToRelation) {
                $this->hydrateMorphToRelation($relation, $attributes[$availableRelation]);
            } elseif ($relation instanceof MorphToManyRelation) {
                $this->hydrateMorphToManyRelation($relation, $attributes[$availableRelation]);
            }
        }
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     * @param string                                        $availableRelation
     *
     * @throws \Swis\JsonApi\Client\Exceptions\HydrationException
     *
     * @return \Swis\JsonApi\Client\Interfaces\OneRelationInterface|\Swis\JsonApi\Client\Interfaces\ManyRelationInterface
     */
    protected function getRelationFromItem(ItemInterface $item, string $availableRelation)
    {
        $method = Util::stringCamel($availableRelation);
        if (!method_exists($item, $method)) {
            throw new HydrationException(sprintf('Method %s not found on %s', $method, get_class($item)));
        }

        return $item->$method();
    }

    /**
     * @param \Swis\JsonApi\Client\Relations\HasOneRelation $relation
     * @param array|string                                  $attributes
     *
     * @throws \Swis\JsonApi\Client\Exceptions\HydrationException
     */
    protected function hydrateHasOneRelation(HasOneRelation $relation, $attributes): void
    {
        if (is_array($attributes)) {
            $relationItem = $this->buildItem($relation->getType(), $attributes);
        } else {
            $relationItem = $this->buildItem($relation->getType(), ['id' => $attributes]);
        }

        $relation->associate($relationItem);
    }

    /**
     * @param \Swis\JsonApi\Client\Relations\HasManyRelation $relation
     * @param array                                          $attributes
     *
     * @throws \Swis\JsonApi\Client\Exceptions\HydrationException
     */
    protected function hydrateHasManyRelation(HasManyRelation $relation, array $attributes): void
    {
        foreach ($attributes as $relationData) {
            if (is_array($relationData)) {
                $relationItem = $this->buildItem($relation->getType(), $relationData);
            } else {
                $relationItem = $this->buildItem($relation->getType(), ['id' => $relationData]);
            }

            $relation->associate($relation->getIncluded()->push($relationItem));
        }
    }

    /**
     * @param \Swis\JsonApi\Client\Relations\MorphToRelation $relation
     * @param array                                          $attributes
     *
     * @throws \Swis\JsonApi\Client\Exceptions\HydrationException
     */
    protected function hydrateMorphToRelation(MorphToRelation $relation, array $attributes): void
    {
        if (!array_key_exists('type', $attributes)) {
            throw new HydrationException('Always provide a "type" attribute in a morphTo relationship');
        }
        $relationItem = $this->buildItem($attributes['type'], Util::arrayExcept($attributes, 'type'));

        $relation->associate($relationItem);
    }

    /**
     * @param \Swis\JsonApi\Client\Relations\MorphToManyRelation $relation
     * @param array                                              $attributes
     *
     * @throws \Swis\JsonApi\Client\Exceptions\HydrationException
     */
    protected function hydrateMorphToManyRelation(MorphToManyRelation $relation, array $attributes): void
    {
        foreach ($attributes as $relationData) {
            if (!array_key_exists('type', $relationData)) {
                throw new HydrationException('Always provide a "type" attribute in a morphToMany relationship entry');
            }
            $relationItem = $this->buildItem($relationData['type'], Util::arrayExcept($relationData, 'type'));

            $relation->associate($relation->getIncluded()->push($relationItem));
        }
    }

    /**
     * @param string $type
     * @param array  $attributes
     *
     * @throws \Swis\JsonApi\Client\Exceptions\HydrationException
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface
     */
    protected function buildItem(string $type, array $attributes): ItemInterface
    {
        $item = (new Item())->setType($type);
        if ($this->typeMapper->hasMapping($type)) {
            $item = $this->typeMapper->getMapping($type);
        }

        return $this->hydrate($item, $attributes, (string) $attributes['id']);
    }
}
