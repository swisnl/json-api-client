<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;

interface ItemInterface extends DataInterface
{
    public function getId(): ?string;

    public function hasId(): bool;

    public function isNew(): bool;

    /**
     * @return static
     */
    public function setId(?string $id);

    public function getType(): string;

    public function hasType(): bool;

    /**
     * @return static
     */
    public function setType(string $type);

    /**
     * @return $this
     */
    public function setLinks(?Links $links);

    public function getLinks(): ?Links;

    /**
     * @return $this
     */
    public function setMeta(?Meta $meta);

    public function getMeta(): ?Meta;

    /**
     * @return static
     */
    public function fill(array $attributes);

    /**
     * @return mixed
     */
    public function forceFill(array $attributes);

    /**
     * @return array
     */
    public function getAttributes();

    /**
     * @return mixed
     */
    public function getAttribute($key);

    public function setAttribute($key, $value);

    public function hasAttribute($key): bool;

    public function hasAttributes(): bool;

    public function hasRelationships(): bool;

    public function getAvailableRelations(): array;

    /**
     * Set the specific relationship in the model.
     *
     * @param  \Swis\JsonApi\Client\Interfaces\DataInterface|false|null  $value
     * @return static
     */
    public function setRelation(string $relation, $value = false, ?Links $links = null, ?Meta $meta = null);

    /**
     * @return \Swis\JsonApi\Client\Interfaces\OneRelationInterface[]|\Swis\JsonApi\Client\Interfaces\ManyRelationInterface[]
     */
    public function getRelations(): array;
}
