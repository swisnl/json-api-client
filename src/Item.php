<?php

namespace Swis\JsonApi\Client;

use Illuminate\Support\Str;
use Jenssegers\Model\Model;
use Swis\JsonApi\Client\Interfaces\DataInterface;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Interfaces\ManyRelationInterface;
use Swis\JsonApi\Client\Interfaces\OneRelationInterface;
use Swis\JsonApi\Client\Relations\HasManyRelation;
use Swis\JsonApi\Client\Relations\HasOneRelation;
use Swis\JsonApi\Client\Relations\MorphToManyRelation;
use Swis\JsonApi\Client\Relations\MorphToRelation;
use Swis\JsonApi\Client\Traits\HasLinks;
use Swis\JsonApi\Client\Traits\HasMeta;
use Swis\JsonApi\Client\Traits\HasType;

/**
 * @property string|null id
 */
class Item extends Model implements ItemInterface
{
    use HasLinks;
    use HasMeta;
    use HasType;

    /**
     * @var string|null
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

        $links = $this->getLinks();
        if ($links !== null) {
            $data['links'] = $links->toArray();
        }

        $meta = $this->getMeta();
        if ($meta !== null) {
            $data['meta'] = $meta->toArray();
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
     * @return string|null
     */
    public function getId(): ? string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     *
     * @return static
     */
    public function setId(? string $id)
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
     * @return bool
     */
    public function hasRelationships(): bool
    {
        return !empty($this->getRelationships());
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        if ($key === 'id') {
            return $this->getId();
        }

        return parent::__get($key);
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
     * @return bool
     */
    public function hasAttributes(): bool
    {
        return !empty($this->toArray());
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function __set($key, $value)
    {
        if ($key === 'id') {
            $this->setId($value);

            return;
        }

        parent::__set($key, $value);
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
        $method = Str::camel($key);
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
        if ($key === 'id') {
            return $this->hasId();
        }

        return parent::__isset($key) || $this->hasRelationship($key) || $this->hasRelationship(Str::snake($key));
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
        $relationName = $relationName ?: Str::snake(debug_backtrace()[1]['function']);

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
        $relationName = $relationName ?: Str::snake(debug_backtrace()[1]['function']);

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
        $relationName = $relationName ?: Str::snake(debug_backtrace()[1]['function']);

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
        $relationName = $relationName ?: Str::snake(debug_backtrace()[1]['function']);

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
     * @param \Swis\JsonApi\Client\Links|null               $links
     * @param \Swis\JsonApi\Client\Meta|null                $meta
     *
     * @return static
     */
    public function setRelation(string $relation, DataInterface $value, Links $links = null, Meta $meta = null)
    {
        if (method_exists($this, $relation)) {
            /** @var \Swis\JsonApi\Client\Interfaces\OneRelationInterface|\Swis\JsonApi\Client\Interfaces\ManyRelationInterface $relationObject */
            $relationObject = $this->$relation();
        } elseif ($value instanceof Collection) {
            $relationObject = $this->morphToMany(Str::snake($relation));
        } else {
            $relationObject = $this->morphTo(Str::snake($relation));
        }

        $relationObject->associate($value);
        $relationObject->setLinks($links);
        $relationObject->setMeta($meta);

        return $this;
    }

    /**
     * @return array
     */
    public function getRelations(): array
    {
        return $this->relationships;
    }
}
