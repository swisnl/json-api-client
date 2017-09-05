<?php

namespace Swis\JsonApi\Interfaces;

interface ItemInterface extends DataInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @return bool
     */
    public function hasId(): bool;

    /**
     * @return bool
     */
    public function isNew(): bool;

    /**
     * @param string $id
     *
     * @return static
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $type
     *
     * @return static
     */
    public function setType(string $type);

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
     * @return array
     */
    public function getAvailableRelations(): array;

    /**
     * Set the specific relationship in the model.
     *
     * @param string $relation
     * @param mixed  $value
     *
     * @return static
     */
    public function setRelation($relation, $value);

    /**
     * @TODO: MEGA TODO. Set up a serializer for the Item so that we can remove this, getRelationships etc
     *
     * @throws \Exception
     *
     * @return \Swis\JsonApi\Collection
     */
    public function getIncluded();
}
