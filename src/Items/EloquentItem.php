<?php

namespace Swis\JsonApi\Client\Items;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as IlluminateCollection;
use Illuminate\Support\Str;
use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Interfaces\ItemInterface;

class EloquentItem extends Model implements ItemInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $availableRelations = [];

    /**
     * @return string
     */
    public function getId()
    {
        return $this->getKey();
    }

    /**
     * @return bool
     */
    public function hasId(): bool
    {
        return null !== $this->getKey();
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return !$this->exists;
    }

    /**
     * @param string $id
     *
     * @return static
     */
    public function setId($id)
    {
        $this->setAttribute($this->getKeyName(), $id);

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        if (property_exists($this, 'type') && $this->type) {
            return $this->type;
        }

        return str_replace('\\', '', Str::snake(class_basename($this)));
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
     * Get the data as a plain json api array.
     *
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

        $attributes = $this->getAttributes();
        unset($attributes[$this->getKeyName()]);
        if (!empty($attributes)) {
            $data['attributes'] = $attributes;
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getAvailableRelations(): array
    {
        return $this->availableRelations;
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

        foreach ($this->getRelations() as $name => $relation) {
            if ($relation instanceof ItemInterface) {
                if (!empty($relation->getType()) && null !== $relation->getId()) {
                    $included->push($relation->toJsonApiArray());
                }
                $included = $included->merge($relation->getIncluded());
            } elseif ($relation instanceof IlluminateCollection) {
                $relation->each(
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
}
