<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Concerns;

use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Interfaces\DataInterface;
use Swis\JsonApi\Client\Interfaces\ManyRelationInterface;
use Swis\JsonApi\Client\Interfaces\OneRelationInterface;
use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;
use Swis\JsonApi\Client\Relations\HasManyRelation;
use Swis\JsonApi\Client\Relations\HasOneRelation;
use Swis\JsonApi\Client\Relations\MorphToManyRelation;
use Swis\JsonApi\Client\Relations\MorphToRelation;
use Swis\JsonApi\Client\Util;

trait HasRelations
{
    /**
     * @var \Swis\JsonApi\Client\Interfaces\OneRelationInterface[]|\Swis\JsonApi\Client\Interfaces\ManyRelationInterface[]
     */
    protected $relations = [];

    /**
     * Create a singular relation to another item.
     *
     * @template TItem of \Swis\JsonApi\Client\Interfaces\ItemInterface
     *
     * @param  class-string<TItem>  $itemClass
     * @return \Swis\JsonApi\Client\Interfaces\OneRelationInterface<TItem>
     */
    public function hasOne(string $itemClass, ?string $name = null): OneRelationInterface
    {
        $name = $name ?: $this->guessRelationName();

        if (! array_key_exists($name, $this->relations)) {
            $this->relations[$name] = $this->newHasOne((new $itemClass)->getType());
        }

        return $this->relations[$name];
    }

    protected function newHasOne(string $type): OneRelationInterface
    {
        return new HasOneRelation($type);
    }

    /**
     * Create a plural relation to another item.
     *
     * @template TItem of \Swis\JsonApi\Client\Interfaces\ItemInterface
     *
     * @param  class-string<TItem>  $itemClass
     * @return \Swis\JsonApi\Client\Interfaces\ManyRelationInterface<TItem>
     */
    public function hasMany(string $itemClass, ?string $name = null): ManyRelationInterface
    {
        $name = $name ?: $this->guessRelationName();

        if (! array_key_exists($name, $this->relations)) {
            $this->relations[$name] = $this->newHasMany((new $itemClass)->getType());
        }

        return $this->relations[$name];
    }

    protected function newHasMany(string $type): ManyRelationInterface
    {
        return new HasManyRelation($type);
    }

    /**
     * Create a singular relation.
     *
     *
     * @return \Swis\JsonApi\Client\Relations\MorphToRelation
     */
    public function morphTo(?string $name = null): OneRelationInterface
    {
        $name = $name ?: $this->guessRelationName();

        if (! array_key_exists($name, $this->relations)) {
            $this->relations[$name] = $this->newMorphTo();
        }

        return $this->relations[$name];
    }

    protected function newMorphTo(): OneRelationInterface
    {
        return new MorphToRelation;
    }

    /**
     * Create a plural relation.
     *
     *
     * @return \Swis\JsonApi\Client\Relations\MorphToManyRelation
     */
    public function morphToMany(?string $name = null): ManyRelationInterface
    {
        $name = $name ?: $this->guessRelationName();

        if (! array_key_exists($name, $this->relations)) {
            $this->relations[$name] = $this->newMorphToMany();
        }

        return $this->relations[$name];
    }

    protected function newMorphToMany(): ManyRelationInterface
    {
        return new MorphToManyRelation;
    }

    /**
     * Guess the relationship name.
     */
    protected function guessRelationName(): string
    {
        [$one, $two, $caller] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);

        return Util::stringSnake($caller['function']);
    }

    /**
     * @return \Swis\JsonApi\Client\Interfaces\OneRelationInterface[]|\Swis\JsonApi\Client\Interfaces\ManyRelationInterface[]
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    /**
     * @return \Swis\JsonApi\Client\Interfaces\OneRelationInterface|\Swis\JsonApi\Client\Interfaces\ManyRelationInterface|null
     */
    public function getRelation(string $name)
    {
        return $this->relations[$name] ?? null;
    }

    /**
     * Get the relationship data (included).
     */
    public function getRelationValue(string $name): ?DataInterface
    {
        // If the "attribute" exists as a method on the model, we will just assume
        // it is a relationship and will load and return the included items in the relationship
        $method = Util::stringCamel($name);
        if (method_exists($this, $method)) {
            return $this->$method()->getAssociated();
        }

        // If the "attribute" exists as a relationship on the model, we will return
        // the included items in the relationship
        if ($this->hasRelation($name)) {
            return $this->getRelation($name)->getAssociated();
        }

        return null;
    }

    /**
     * Set the specific relationship on the model.
     *
     * @param  \Swis\JsonApi\Client\Interfaces\DataInterface|false|null  $value
     * @return static
     */
    public function setRelation(string $relation, $value = false, ?Links $links = null, ?Meta $meta = null)
    {
        $method = Util::stringCamel($relation);
        if (method_exists($this, $method)) {
            /** @var \Swis\JsonApi\Client\Interfaces\OneRelationInterface|\Swis\JsonApi\Client\Interfaces\ManyRelationInterface $relationObject */
            $relationObject = $this->$method();
        } elseif ($value instanceof Collection) {
            $relationObject = $this->morphToMany($relation);
        } else {
            $relationObject = $this->morphTo($relation);
        }

        if ($value !== false) {
            $relationObject->dissociate();
            if ($value !== null) {
                $relationObject->associate($value);
            }
        }
        $relationObject->setLinks($links);
        $relationObject->setMeta($meta);

        return $this;
    }

    public function hasRelation(string $name): bool
    {
        return array_key_exists($name, $this->relations);
    }

    /**
     * @return static
     */
    public function unsetRelation(string $name)
    {
        unset($this->relations[$name]);

        return $this;
    }
}
