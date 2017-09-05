<?php

namespace Swis\JsonApi;

use Swis\JsonApi\Errors\ErrorCollection;
use Swis\JsonApi\Interfaces\DocumentInterface;

class InvalidResponseDocument implements DocumentInterface
{
    /**
     * @return \Swis\JsonApi\Interfaces\DataInterface
     */
    public function getData()
    {
        return null;
    }

    /**
     * @return \Swis\JsonApi\Errors\ErrorCollection
     */
    public function getErrors(): ErrorCollection
    {
        return new ErrorCollection();
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
     * @return bool
     */
    public function hasErrors(): bool
    {
        return false;
    }
}
