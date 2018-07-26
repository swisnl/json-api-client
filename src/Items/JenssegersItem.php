<?php

namespace Swis\JsonApi\Client\Items;

use Jenssegers\Model\Model;
use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Interfaces\RelationInterface;
use Swis\JsonApi\Client\Relations\HasManyRelation;
use Swis\JsonApi\Client\Relations\HasOneRelation;
use Swis\JsonApi\Client\Relations\MorphToManyRelation;
use Swis\JsonApi\Client\Relations\MorphToRelation;

class JenssegersItem extends Model implements ItemInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var
     */
    protected $id;

    /**
     * Contains the initial values (Which fields are pre-filled on CREATE-form).
     *
     * @var array
     */
    protected $initial = [];

    /**
     * @var \Swis\JsonApi\Client\Interfaces\RelationInterface[]
     */
    protected $relationships = [];

    /**
     * Available relations need to be explicitly set.
     *
     * @var array
     */
    protected $availableRelations = [];

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @return array
     */
    public function toJsonApiArray(): array
    {
        $data = [
            'type' => $this->getType(),
        ];

        if (!$this->isNew()) {
            $data['id'] = $this->getId();
        }

        $attributes = $this->toArray();
        if (!empty($attributes)) {
            $data['attributes'] = $attributes;
        }

        $relationships = $this->getRelationships();
        if (!empty($relationships)) {
            $data['relationships'] = $relationships;
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return static
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return !$this->hasId();
    }

    /**
     * @return bool
     */
    public function hasId(): bool
    {
        return isset($this->id);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return static
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return array
     */
    public function getRelationships(): array
    {
        $relationships = [];

        /** @var \Swis\JsonApi\Client\Interfaces\RelationInterface $relationship */
        foreach ($this->relationships as $name => $relationship) {
            if ($relationship instanceof HasOneRelation) {
                $relationships[$name] = [
                    'data' => [
                        'type' => $relationship->getType(),
                        'id'   => $relationship->getId(),
                    ],
                ];

            } elseif ($relationship instanceof HasManyRelation) {
                $relationships[$name]['data'] = [];

                foreach ($relationship->getIncluded() as $item) {
                    $data = [
                        'type' => $relationship->getType(),
                        'id'   => $item->getId(),
                    ];
                    $relationships[$name]['data'][] = $data + $item->getAttributes();
                }

            } elseif ($relationship instanceof MorphToRelation) {
                $relationships[$name]['data'] = [];
                $data = [
                    'type' => $relationship->getIncluded()->getType(),
                    'id'   => $relationship->getIncluded()->getId(),
                ];
                $relationships[$name]['data'] = $data + $relationship->getIncluded()->getAttributes();

            } elseif ($relationship instanceof MorphToManyRelation) {
                $relationships[$name]['data'] = [];

                foreach ($relationship->getIncluded() as $item) {
                    $data = [
                        'type' => $item->getType(),
                        'id'   => $item->getId(),
                    ];
                    $relationships[$name]['data'][] = $data + $item->getAttributes();
                }
            }
        }

        return $relationships;
    }


    /**
     * @TODO: MEGA TODO. Set up a serializer for the Item so that we can remove this, getRelationships etc
     *
     * @throws \Exception
     *
     * @return \Swis\JsonApi\Client\Collection
     */
    public function getIncluded(): Collection
    {
        $included = new Collection();

        foreach ($this->relationships as $name => $relationship) {
            if ($relationship->shouldOmitIncluded() || !$relationship->hasIncluded()) {
                continue;
            }

            $includedFromRelationship = $relationship->getIncluded();
            if ($includedFromRelationship instanceof ItemInterface) {
                if (!empty($includedFromRelationship->getType()) && null !== $includedFromRelationship->getId()) {
                    $included->push($includedFromRelationship->toJsonApiArray());
                }
                $included = $included->merge($includedFromRelationship->getIncluded());
            } elseif ($includedFromRelationship instanceof Collection) {
                $includedFromRelationship->each(
                    function (ItemInterface $item) use (&$included) {
                        if (!empty($item->getType()) && null !== $item->getId()) {
                            $included->push($item->toJsonApiArray());
                        }
                        $included = $included->merge($item->getIncluded());
                    }
                );
            } else {
                throw new \Exception('Not yet implemented');
            }
        }

        return $included->unique(
            function (array $item) {
                return $item['type'].':'.$item['id'];
            }
        );
    }

    /**
     * @param string $key
     *
     * @return \Swis\JsonApi\Client\Interfaces\DataInterface|mixed
     */
    public function getAttribute($key)
    {
        if ($this->hasAttribute($key) || $this->hasGetMutator($key)) {
            return parent::getAttribute($key);
        }

        return $this->getRelationValue($key);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasAttribute($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Get the relationship data.
     *
     * @param string $key
     *
     * @return \Swis\JsonApi\Client\Interfaces\DataInterface
     */
    public function getRelationValue($key)
    {
        // If the "attribute" exists as a method on the model, we will just assume
        // it is a relationship and will load and return the included items in the relationship
        $method = camel_case($key);
        if (method_exists($this, $method)) {
            return $this->$method()->getIncluded();
        }

        // If the "attribute" exists as a relationship on the model, we will return
        // the included items in the relationship
        if ($this->hasRelationship($key)) {
            return $this->getRelationship($key)->getIncluded();
        }
    }

    /**
     * Determine if an attribute exists on the model.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        $result = (isset($this->attributes[$key]) || isset($this->relationships[snake_case($key)])) ||
            ($this->hasGetMutator($key) && !is_null($this->getAttributeValue($key)));

        return $result;
    }

    /**
     * @param $name
     *
     * @return \Swis\JsonApi\Client\Interfaces\RelationInterface
     */
    public function getRelationship(string $name): RelationInterface
    {
        return $this->relationships[$name];
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasRelationship(string $name): bool
    {
        return array_key_exists($name, $this->relationships);
    }

    /**
     * Create a singular relation to another item.
     *
     * @param string      $class
     * @param string|null $relationName
     *
     * @return \Swis\JsonApi\Client\Relations\HasOneRelation
     */
    public function hasOne(string $class, string $relationName = null)
    {
        $relationName = $relationName ?: snake_case(debug_backtrace()[1]['function']);
        $itemType = (new $class())->getType();

        if (!array_key_exists($relationName, $this->relationships)) {
            $this->relationships[$relationName] = new HasOneRelation($itemType, $this);
        }

        return $this->relationships[$relationName];
    }

    /**
     * Create a plural relation to another item.
     *
     * @param string      $class
     * @param string|null $relationName
     *
     * @return \Swis\JsonApi\Client\Relations\HasManyRelation
     */
    public function hasMany(string $class, string $relationName = null)
    {
        $relationName = $relationName ?: snake_case(debug_backtrace()[1]['function']);
        $itemType = (new $class())->getType();

        if (!array_key_exists($relationName, $this->relationships)) {
            $this->relationships[$relationName] = new HasManyRelation($itemType);
        }

        return $this->relationships[$relationName];
    }

    /**
     * Create a singular relation to another item.
     *
     * @param string|null $relationName
     *
     * @return \Swis\JsonApi\Client\Relations\MorphToRelation
     */
    public function morphTo(string $relationName = null)
    {
        $relationName = $relationName ?: snake_case(debug_backtrace()[1]['function']);

        if (!array_key_exists($relationName, $this->relationships)) {
            $this->relationships[$relationName] = new MorphToRelation($this);
        }

        return $this->relationships[$relationName];
    }

    /**
     * Create a plural relation to another item.
     *
     * @param string|null $relationName
     *
     * @return \Swis\JsonApi\Client\Relations\MorphToManyRelation
     */
    public function morphToMany(string $relationName = null)
    {
        $relationName = $relationName ?: snake_case(debug_backtrace()[1]['function']);

        if (!array_key_exists($relationName, $this->relationships)) {
            $this->relationships[$relationName] = new MorphToManyRelation();
        }

        return $this->relationships[$relationName];
    }

    /**
     * Sets the initial values of an Item.
     *
     * @param array $initial
     *
     * @return static
     */
    public function setInitial(array $initial)
    {
        $this->initial = $initial;

        return $this;
    }

    /**
     * Returns the initial values of an Item.
     *
     * @param string|null $key
     *
     * @return array|mixed
     */
    public function getInitial($key = null)
    {
        if (null === $key) {
            return $this->initial;
        }

        return $this->initial[$key];
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasInitial($key): bool
    {
        return isset($this->getInitial()[$key]);
    }

    /**
     * Prefills the model with values from $initial, when adding new item.
     *
     * @return static
     */
    public function useInitial()
    {
        $this->fill($this->initial);

        return $this;
    }

    /**
     * @return array
     */
    public function getAvailableRelations(): array
    {
        return $this->availableRelations;
    }

    /**
     * Set the specific relationship in the model.
     *
     * @param string $relation
     * @param mixed  $value
     *
     * @return static
     */
    public function setRelation($relation, $value)
    {
        if (method_exists($this, $relation)) {
            /** @var \Swis\JsonApi\Client\Interfaces\RelationInterface $relationObject */
            $relationObject = $this->$relation();
        } else {
            if ($value instanceof Collection) {
                $relationObject = $this->morphToMany(snake_case($relation));
            } else {
                $relationObject = $this->morphTo(snake_case($relation));
            }
        }

        $relationObject->associate($value);

        return $this;
    }
}
