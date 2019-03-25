<?php

namespace Swis\JsonApi\Client\Interfaces;

use Psr\Http\Message\ResponseInterface;
use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\ErrorCollection;
use Swis\JsonApi\Client\Jsonapi;
use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;

interface DocumentInterface extends \JsonSerializable
{
    /**
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function getResponse(): ? ResponseInterface;

    /**
     * @return \Swis\JsonApi\Client\Interfaces\DataInterface
     */
    public function getData();

    /**
     * @return \Swis\JsonApi\Client\ErrorCollection
     */
    public function getErrors(): ErrorCollection;

    /**
     * @return bool
     */
    public function hasErrors(): bool;

    /**
     * @return \Swis\JsonApi\Client\Meta|null
     */
    public function getMeta(): ? Meta;

    /**
     * @return \Swis\JsonApi\Client\Links|null
     */
    public function getLinks(): ? Links;

    /**
     * @return mixed
     */
    public function getIncluded(): Collection;

    /**
     * @return \Swis\JsonApi\Client\Jsonapi|null
     */
    public function getJsonapi(): ? Jsonapi;

    /**
     * @return array
     */
    public function toArray(): array;
}
