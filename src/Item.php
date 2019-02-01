<?php

namespace Swis\JsonApi\Client;

use Jenssegers\Model\Model;
use Swis\JsonApi\Client\Interfaces\DataInterface;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Interfaces\ManyRelationInterface;
use Swis\JsonApi\Client\Interfaces\OneRelationInterface;
use Swis\JsonApi\Client\Relations\HasManyRelation;
use Swis\JsonApi\Client\Relations\HasOneRelation;
use Swis\JsonApi\Client\Relations\MorphToManyRelation;
use Swis\JsonApi\Client\Relations\MorphToRelation;
use Swis\JsonApi\Client\Traits\HasType;

class Item extends Model implements ItemInterface
{
    use HasType;

    /**
     * @var
     */
    protected $id;

    /**
     * Contains the initial values.
     *
     * @var array
     */
    protected $initial = [];

    /**
     * @var \Swis\JsonApi\Client\Interfaces\OneRelationInterface[]|\Swis\JsonApi\Client\Interfaces\ManyRelationInterface[]
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

        if ($this->hasId()) {
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

        foreach ($this->relationships as $name => $relationship) {
            if ($relationship instanceof OneRelationInterface) {
                $relationships[$name] = ['data' => null];

                if ($relationship->getIncluded() !== null) {
                    $relationships[$name] = [
                        'data' => [
                            'type' => $relationship->getIncluded()->getType(),
                            'id'   => $relationship->getIncluded()->getId(),
                        ],
                    ];
                }
            } elseif ($relationship instanceof ManyRelationInterface) {
                $relationships[$name]['data'] = [];

                foreach ($relationship->getIncluded() as $item) {
                    $relationships[$name]['data'][] =
                        [
                            'type' => $item->getType(),
                            'id'   => $item->getId(),
                        ];
                }
            }
        }

        return $relationships;
    }

    /**
     * @TODO: MEGA TODO. Set up a serializer for the Item so that we can remove this, getRelationships etc
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

            if ($relationship instanceof OneRelationInterface) {
                /** @var \Swis\JsonApi\Client\Interfaces\ItemInterface $item */
                $item = $relationship->getIncluded();
                if ($item->canBeIncluded()) {
                    $included->push($item->toJsonApiArray());
                }
                $included = $included->merge($item->getIncluded());
            } elseif ($relationship instanceof ManyRelationInterface) {
                $relationship->getIncluded()->each(
                    function (ItemInterface $item) use (&$included) {
                        if ($item->canBeIncluded()) {
                            $included->push($item->toJsonApiArray());
                        }
                        $included = $included->merge($item->getIncluded());
                    }
                );
            }
        }

        return $included
            ->unique(
                function (array $item) {
                    return $item['type'].':'.$item['id'];
                }
            )
            ->values();
    }

    /**
     * @return bool
     */
    public function canBeIncluded(): bool
    {
        if (empty($this->getType())) {
            return false;
        }

        if (null === $this->getId()) {
            return false;
        }

        if (empty($this->relationships) && empty($this->toArray())) {
            return false;
        }

        return true;
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
    public function hasAttribute($key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Get the relationship data.
     *
     * @param string $key
     *
     * @return \Swis\JsonApi\Client\Interfaces\DataInterface|null
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

        return null;
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
        return parent::__isset($key) || $this->hasRelationship($key) || $this->hasRelationship(snake_case($key));
    }

    /**
     * @param string $name
     *
     * @return \Swis\JsonApi\Client\Interfaces\OneRelationInterface|\Swis\JsonApi\Client\Interfaces\ManyRelationInterface
     */
    public function getRelationship(string $name)
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
     * @param $name
     *
     * @return static
     */
    public function removeRelationship(string $name)
    {
        unset($this->relationships[$name]);

        return $this;
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

        if (!array_key_exists($relationName, $this->relationships)) {
            $this->relationships[$relationName] = new HasOneRelation((new $class())->getType());
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

        if (!array_key_exists($relationName, $this->relationships)) {
            $this->relationships[$relationName] = new HasManyRelation((new $class())->getType());
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
            $this->relationships[$relationName] = new MorphToRelation();
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
     * Set the specific relationship on the model.
     *
     * @param string                                        $relation
     * @param \Swis\JsonApi\Client\Interfaces\DataInterface $value
     *
     * @return static
     */
    public function setRelation(string $relation, DataInterface $value)
    {
        if (method_exists($this, $relation)) {
            /** @var \Swis\JsonApi\Client\Interfaces\OneRelationInterface|\Swis\JsonApi\Client\Interfaces\ManyRelationInterface $relationObject */
            $relationObject = $this->$relation();
        } elseif ($value instanceof Collection) {
            $relationObject = $this->morphToMany(snake_case($relation));
        } else {
            $relationObject = $this->morphTo(snake_case($relation));
        }

        $relationObject->associate($value);

        return $this;
    }
}
