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
     * @param \Psr\Http\Message\ResponseInterface|null $response
     *
     * @return $this
     */
    public function setResponse(? ResponseInterface $response);

    /**
     * @return \Swis\JsonApi\Client\Interfaces\DataInterface
     */
    public function getData();

    /**
     * @param \Swis\JsonApi\Client\Interfaces\DataInterface $data
     *
     * @return $this
     */
    public function setData(DataInterface $data);

    /**
     * @return \Swis\JsonApi\Client\ErrorCollection
     */
    public function getErrors(): ErrorCollection;

    /**
     * @param \Swis\JsonApi\Client\ErrorCollection $errors
     *
     * @return $this
     */
    public function setErrors(ErrorCollection $errors);

    /**
     * @return bool
     */
    public function hasErrors(): bool;

    /**
     * @return \Swis\JsonApi\Client\Meta|null
     */
    public function getMeta(): ? Meta;

    /**
     * @param \Swis\JsonApi\Client\Meta|null $meta
     *
     * @return $this
     */
    public function setMeta(? Meta $meta);

    /**
     * @return \Swis\JsonApi\Client\Links|null
     */
    public function getLinks(): ? Links;

    /**
     * @param \Swis\JsonApi\Client\Links|null $links
     *
     * @return $this
     */
    public function setLinks(? Links $links);

    /**
     * @return mixed
     */
    public function getIncluded(): Collection;

    /**
     * @param \Swis\JsonApi\Client\Collection $included
     *
     * @return $this
     */
    public function setIncluded(Collection $included);

    /**
     * @return \Swis\JsonApi\Client\Jsonapi|null
     */
    public function getJsonapi(): ? Jsonapi;

    /**
     * @param \Swis\JsonApi\Client\Jsonapi|null $jsonapi
     *
     * @return $this
     */
    public function setJsonapi(? Jsonapi $jsonapi);

    /**
     * @return array
     */
    public function toArray(): array;
}
