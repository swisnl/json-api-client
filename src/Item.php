<?php

namespace Swis\JsonApi\Client;

use Illuminate\Support\Str;
use Jenssegers\Model\Model;
use Swis\JsonApi\Client\Concerns\HasId;
use Swis\JsonApi\Client\Concerns\HasInitial;
use Swis\JsonApi\Client\Concerns\HasLinks;
use Swis\JsonApi\Client\Concerns\HasMeta;
use Swis\JsonApi\Client\Concerns\HasRelations;
use Swis\JsonApi\Client\Concerns\HasType;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Interfaces\ManyRelationInterface;
use Swis\JsonApi\Client\Interfaces\OneRelationInterface;

/**
 * @property string|null id
 */
class Item extends Model implements ItemInterface
{
    use HasId;
    use HasInitial;
    use HasLinks;
    use HasMeta;
    use HasRelations;
    use HasType;

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
     * @param $key
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
            if ($relation->hasIncluded()) {
                if ($relation instanceof OneRelationInterface) {
                    $relationships[$name]['data'] = null;

                    if ($relation->getIncluded() !== null) {
                        $relationships[$name]['data'] = [
                            'type' => $relation->getIncluded()->getType(),
                            'id' => $relation->getIncluded()->getId(),
                        ];
                    }
                } elseif ($relation instanceof ManyRelationInterface) {
                    $relationships[$name]['data'] = [];

                    foreach ($relation->getIncluded() as $item) {
                        $relationships[$name]['data'][] = [
                            'type' => $item->getType(),
                            'id' => $item->getId(),
                        ];
                    }
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

        return parent::__isset($key) || $this->hasRelation($key) || $this->hasRelation(Str::snake($key));
    }

    /**
     * Unset an attribute on the model.
     *
     * @param string $key
     */
    public function __unset($key)
    {
        if ($key === 'id') {
            $this->id = null;
        }

        unset($this->attributes[$key]);
    }
}
