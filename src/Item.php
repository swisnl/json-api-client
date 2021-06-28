<?php

namespace Swis\JsonApi\Client;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use Swis\JsonApi\Client\Exceptions\MassAssignmentException;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Interfaces\ManyRelationInterface;
use Swis\JsonApi\Client\Interfaces\OneRelationInterface;

/**
 * @property string|null id
 */
class Item implements ArrayAccess, Arrayable, Jsonable, JsonSerializable, ItemInterface
{
    use Concerns\GuardsAttributes;
    use Concerns\HasAttributes;
    use Concerns\HasId;
    use Concerns\HasInitial;
    use Concerns\HasLinks;
    use Concerns\HasMeta;
    use Concerns\HasRelations;
    use Concerns\HasType;
    use Concerns\HidesAttributes;

    /**
     * Available relations need to be explicitly set.
     *
     * @var array
     */
    protected $availableRelations = [];

    /**
     * Create a new Item instance.
     *
     * @param array $attributes
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @param array $attributes
     *
     * @throws \Swis\JsonApi\Client\Exceptions\MassAssignmentException
     *
     * @return $this
     */
    public function fill(array $attributes)
    {
        $totallyGuarded = $this->totallyGuarded();

        foreach ($this->fillableFromArray($attributes) as $key => $value) {
            // The developers may choose to place some attributes in the "fillable" array
            // which means only those attributes may be set through mass assignment to
            // the model, and all others will just get ignored for security reasons.
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            } elseif ($totallyGuarded) {
                throw new MassAssignmentException(sprintf('Add [%s] to fillable property to allow mass assignment on [%s].', $key, get_class($this)));
            }
        }

        return $this;
    }

    /**
     * Fill the model with an array of attributes. Force mass assignment.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function forceFill(array $attributes)
    {
        return static::unguarded(function () use ($attributes) {
            return $this->fill($attributes);
        });
    }

    /**
     * Create a new instance of the given model.
     *
     * @param array $attributes
     *
     * @return static
     */
    public function newInstance(array $attributes = [])
    {
        $model = new static($attributes);

        if ($this->type) {
            $model->setType($this->type);
        }
        $model->mergeCasts($this->casts);

        return $model;
    }

    /**
     * Create a list of models from plain arrays.
     *
     * @param array $items
     *
     * @return array
     */
    public static function hydrate(array $items): array
    {
        $instance = new static();

        return array_map(static function ($item) use ($instance) {
            return $instance->newInstance($item);
        }, $items);
    }

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
     * Convert the model instance to JSON.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributesToArray();
    }

    /**
     * Clone the model into a new, non-existing instance.
     *
     * @param array|null $except
     *
     * @return static
     */
    public function replicate(array $except = null)
    {
        $attributes = Util::arrayExcept($this->getAttributes(), $except ?? []);

        return new static($attributes);
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
    public function hasAttributes(): bool
    {
        return !empty($this->toArray());
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
     * @return array
     */
    public function getAvailableRelations(): array
    {
        return $this->availableRelations;
    }

    /**
     * @return array
     */
    public function getRelationships(): array
    {
        $relationships = [];

        foreach ($this->getRelations() as $name => $relation) {
            if (!$relation->hasIncluded()) {
                continue;
            }

            if ($relation instanceof OneRelationInterface) {
                $relationships[$name]['data'] = null;

                if ($relation->getIncluded() !== null) {
                    $relationships[$name]['data'] = [
                        'type' => $relation->getIncluded()->getType(),
                        'id' => $relation->getIncluded()->getId(),
                    ];
                    if ($relation->getIncluded()->getMeta()) {
                        $relationships[$name]['data']['meta'] = $relation->getIncluded()->getMeta()->toArray();
                    }
                }
            } elseif ($relation instanceof ManyRelationInterface) {
                $relationships[$name]['data'] = [];

                foreach ($relation->getIncluded() as $item) {
                    $data = [
                        'type' => $item->getType(),
                        'id' => $item->getId(),
                    ];
                    if ($item->getMeta()) {
                        $data['meta'] = $item->getMeta()->toArray();
                    }
                    $relationships[$name]['data'][] = $data;
                }
            }

            $links = $relation->getLinks();
            if ($links !== null) {
                $relationships[$name]['links'] = $links->toArray();
            }

            $meta = $relation->getMeta();
            if ($meta !== null) {
                $relationships[$name]['meta'] = $meta->toArray();
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
     * Fills the model with the values from $initial. This is useful for setting defaults when creating a new item.
     *
     * @return static
     */
    public function useInitial()
    {
        $this->fill($this->initial);

        return $this;
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->offsetGet($key);
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function __set(string $key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        if ($offset === 'id') {
            return $this->hasId();
        }

        return !is_null($this->getAttribute($offset));
    }

    /**
     * Get the value for a given offset.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if ($offset === 'id') {
            return $this->getId();
        }

        return $this->getAttribute($offset);
    }

    /**
     * Set the value for a given offset.
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === 'id') {
            $this->setId($value);

            return;
        }

        $this->setAttribute($offset, $value);
    }

    /**
     * Unset the value for a given offset.
     *
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        if ($offset === 'id') {
            $this->id = null;
        }

        unset($this->attributes[$offset], $this->relations[$offset]);
    }

    /**
     * Determine if an attribute or relation exists on the model.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset(string $key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Unset an attribute on the model.
     *
     * @param string $key
     *
     * @return void
     */
    public function __unset(string $key)
    {
        $this->offsetUnset($key);
    }

    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
