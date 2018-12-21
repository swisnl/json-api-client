<?php

namespace Swis\JsonApi\Client;

use Swis\JsonApi\Client\Errors\ErrorCollection;
use Swis\JsonApi\Client\Interfaces\DocumentInterface;

class InvalidResponseDocument implements DocumentInterface
{
    /**
     * @return \Swis\JsonApi\Client\Interfaces\DataInterface
     */
    public function getData()
    {
        return null;
    }

    /**
     * @return \Swis\JsonApi\Client\Errors\ErrorCollection
     */
    public function getErrors(): ErrorCollection
    {
        return new ErrorCollection();
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return false;
    }

    /**
     * @return mixed
     */
    public function getMeta(): array
    {
        return [];
    }

    /**
     * @return mixed
     */
    public function getLinks(): array
    {
        return [];
    }

    /**
     * @return mixed
     */
    public function getIncluded(): Collection
    {
        return new Collection();
    }

    /**
     * @return mixed
     */
    public function getJsonapi()
    {
        return null;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @see  http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource
     *
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [];
    }
}
