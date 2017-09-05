<?php

namespace Swis\JsonApi;

use Swis\JsonApi\Errors\ErrorCollection;
use Swis\JsonApi\Interfaces\DataInterface;
use Swis\JsonApi\Interfaces\DocumentInterface;

class Document implements DocumentInterface
{
    /**
     * @var \Swis\JsonApi\Interfaces\DataInterface
     */
    protected $data;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @var array
     */
    protected $links = [];

    /**
     * @var \Swis\JsonApi\Errors\ErrorCollection
     */
    protected $errors;

    /**
     * @var \Swis\JsonApi\Collection
     */
    protected $included;

    /**
     * @var array
     */
    protected $jsonapi = [];

    public function __construct()
    {
        $this->errors = new ErrorCollection();
        $this->included = new Collection();
    }

    /**
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * @param array $meta
     */
    public function setMeta(array $meta)
    {
        $this->meta = $meta;
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param array $links
     */
    public function setLinks(array $links)
    {
        $this->links = $links;
    }

    /**
     * @return \Swis\JsonApi\Errors\ErrorCollection
     */
    public function getErrors(): ErrorCollection
    {
        return $this->errors;
    }

    /**
     * @param \Swis\JsonApi\Errors\ErrorCollection $errors
     */
    public function setErrors(ErrorCollection $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !$this->errors->isEmpty();
    }

    /**
     * @return \Swis\JsonApi\Collection
     */
    public function getIncluded(): Collection
    {
        return $this->included;
    }

    /**
     * @param \Swis\JsonApi\Collection $included
     *
     * @return static
     */
    public function setIncluded(Collection $included)
    {
        $this->included = $included;

        return $this;
    }

    /**
     * @return array
     */
    public function getJsonapi(): array
    {
        return $this->jsonapi;
    }

    /**
     * @param array $jsonapi
     */
    public function setJsonapi(array $jsonapi)
    {
        $this->jsonapi = $jsonapi;
    }

    /**
     * @return \Swis\JsonApi\Interfaces\DataInterface
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \Swis\JsonApi\Interfaces\DataInterface $data
     *
     * @return static
     */
    public function setData(DataInterface $data)
    {
        $this->data = $data;

        return $this;
    }
}
