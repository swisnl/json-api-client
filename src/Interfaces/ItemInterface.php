<?php

namespace Swis\JsonApi\Client\Interfaces;

use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;

interface ItemInterface extends DataInterface
{
    /**
     * @return string|null
     */
    public function getId(): ? string;

    /**
     * @return bool
     */
    public function hasId(): bool;

    /**
     * @return bool
     */
    public function isNew(): bool;

    /**
     * @param string|null $id
     *
     * @return static
     */
    public function setId(? string $id);

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return bool
     */
    public function hasType(): bool;

    /**
     * @param string $type
     *
     * @return static
     */
    public function setType(string $type);

    /**
     * @param \Swis\JsonApi\Client\Links|null $links
     *
     * @return $this
     */
    public function setLinks(? Links $links);

    /**
     * @return \Swis\JsonApi\Client\Links|null
     */
    public function getLinks(): ? Links;

    /**
     * @param \Swis\JsonApi\Client\Meta|null $meta
     *
     * @return $this
     */
    public function setMeta(? Meta $meta);

    /**
     * @return \Swis\JsonApi\Client\Meta|null
     */
    public function getMeta(): ? Meta;

    /**
     * @param array $attributes
     *
     * @return static
     */
    public function fill(array $attributes);

    /**
     * @param array $attributes
     *
     * @return mixed
     */
    public function forceFill(array $attributes);

    /**
     * @return array
     */
    public function getAttributes();

    /**
     * @param $key
     *
     * @return mixed
     */
    public function getAttribute($key);

    /**
     * @param $key
     * @param $value
     */
    public function setAttribute($key, $value);

    /**
     * @param $key
     *
     * @return bool
     */
    public function hasAttribute($key): bool;

    /**
     * @return bool
     */
    public function hasAttributes(): bool;

    /**
     * @return bool
     */
    public function hasRelationships(): bool;

    /**
     * @return array
     */
    public function getAvailableRelations(): array;

    /**
     * Set the specific relationship in the model.
     *
     * @param string                                                   $relation
     * @param \Swis\JsonApi\Client\Interfaces\DataInterface|false|null $value
     * @param \Swis\JsonApi\Client\Links|null                          $links
     * @param \Swis\JsonApi\Client\Meta|null                           $meta
     *
     * @return static
     */
    public function setRelation(string $relation, $value = false, Links $links = null, Meta $meta = null);

    /**
     * @return \Swis\JsonApi\Client\Interfaces\OneRelationInterface[]|\Swis\JsonApi\Client\Interfaces\ManyRelationInterface[]
     */
    public function getRelations(): array;
}
