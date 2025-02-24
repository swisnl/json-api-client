<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client;

use Psr\Http\Message\ResponseInterface;
use Swis\JsonApi\Client\Concerns\HasLinks;
use Swis\JsonApi\Client\Concerns\HasMeta;
use Swis\JsonApi\Client\Interfaces\DataInterface;
use Swis\JsonApi\Client\Interfaces\DocumentInterface;

class Document implements DocumentInterface
{
    use HasLinks;
    use HasMeta;

    /**
     * @var \Psr\Http\Message\ResponseInterface|null
     */
    protected $response;

    /**
     * @var \Swis\JsonApi\Client\Interfaces\DataInterface
     */
    protected $data;

    /**
     * @var \Swis\JsonApi\Client\ErrorCollection
     */
    protected $errors;

    /**
     * @var \Swis\JsonApi\Client\Collection
     */
    protected $included;

    /**
     * @var \Swis\JsonApi\Client\Jsonapi|null
     */
    protected $jsonapi;

    public function __construct()
    {
        $this->errors = new ErrorCollection;
        $this->included = new Collection;
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * @return $this
     */
    public function setResponse(?ResponseInterface $response)
    {
        $this->response = $response;

        return $this;
    }

    public function getErrors(): ErrorCollection
    {
        return $this->errors;
    }

    /**
     * @return $this
     */
    public function setErrors(ErrorCollection $errors)
    {
        $this->errors = $errors;

        return $this;
    }

    public function hasErrors(): bool
    {
        return ! $this->errors->isEmpty();
    }

    public function isSuccess(): bool
    {
        return $this->errors->isEmpty();
    }

    public function getIncluded(): Collection
    {
        return $this->included;
    }

    /**
     * @return $this
     */
    public function setIncluded(Collection $included)
    {
        $this->included = $included;

        return $this;
    }

    public function getJsonapi(): ?Jsonapi
    {
        return $this->jsonapi;
    }

    /**
     * @return $this
     */
    public function setJsonapi(?Jsonapi $jsonapi)
    {
        $this->jsonapi = $jsonapi;

        return $this;
    }

    /**
     * @return \Swis\JsonApi\Client\Interfaces\DataInterface
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return $this
     */
    public function setData(DataInterface $data)
    {
        $this->data = $data;

        return $this;
    }

    public function toArray(): array
    {
        $document = [];

        if ($this->getLinks() !== null) {
            $document['links'] = $this->getLinks()->toArray();
        }

        if ($this->getData() !== null) {
            $document['data'] = $this->data->toJsonApiArray();
        }

        if ($this->getIncluded()->isNotEmpty()) {
            $document['included'] = $this->getIncluded()->toJsonApiArray();
        }

        if ($this->getMeta() !== null) {
            $document['meta'] = $this->getMeta()->toArray();
        }

        if ($this->hasErrors()) {
            $document['errors'] = $this->getErrors()->toArray();
        }

        if ($this->getJsonapi() !== null) {
            $document['jsonapi'] = $this->getJsonapi()->toArray();
        }

        return $document;
    }

    /**
     * {@inheritdoc}
     *
     * @return object
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $document = [];

        if ($this->getLinks() !== null) {
            $document['links'] = $this->getLinks();
        }

        if ($this->getData() !== null) {
            $document['data'] = $this->data->toJsonApiArray();
        }

        if ($this->getIncluded()->isNotEmpty()) {
            $document['included'] = $this->getIncluded()->toJsonApiArray();
        }

        if ($this->getMeta() !== null) {
            $document['meta'] = $this->getMeta();
        }

        if ($this->hasErrors()) {
            $document['errors'] = $this->getErrors();
        }

        if ($this->getJsonapi() !== null) {
            $document['jsonapi'] = $this->getJsonapi();
        }

        return (object) $document;
    }
}
