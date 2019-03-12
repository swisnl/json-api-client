<?php

namespace Swis\JsonApi\Client;

use Psr\Http\Message\ResponseInterface;
use Swis\JsonApi\Client\Interfaces\DocumentInterface;

class InvalidResponseDocument implements DocumentInterface
{
    /**
     * @var \Psr\Http\Message\ResponseInterface|null
     */
    protected $response;

    /**
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface|null $response
     */
    public function setResponse(ResponseInterface $response = null)
    {
        $this->response = $response;
    }

    /**
     * @return \Swis\JsonApi\Client\Interfaces\DataInterface
     */
    public function getData()
    {
        return null;
    }

    /**
     * @return \Swis\JsonApi\Client\ErrorCollection
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
     * @return \Swis\JsonApi\Client\Meta|null
     */
    public function getMeta()
    {
        return null;
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
     * @return \Swis\JsonApi\Client\Jsonapi|null
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
