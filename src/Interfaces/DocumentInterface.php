<?php

namespace Swis\JsonApi\Client\Interfaces;

use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Errors\ErrorCollection;

interface DocumentInterface
{
    /**
     * @return \Swis\JsonApi\Client\Interfaces\DataInterface
     */
    public function getData();

    /**
     * @return \Swis\JsonApi\Client\Errors\ErrorCollection
     */
    public function getErrors(): ErrorCollection;

    /**
     * @return mixed
     */
    public function getMeta(): array;

    /**
     * @return mixed
     */
    public function getLinks(): array;

    /**
     * @return mixed
     */
    public function getIncluded(): Collection;

    /**
     * @return mixed
     */
    public function getJsonapi();

    /**
     * @return bool
     */
    public function hasErrors(): bool;
}
