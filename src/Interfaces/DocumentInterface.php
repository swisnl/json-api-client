<?php

namespace Swis\JsonApi\Interfaces;

use Swis\JsonApi\Collection;
use Swis\JsonApi\Errors\ErrorCollection;

interface DocumentInterface
{
    /**
     * @return DataInterface
     */
    public function getData();

    /**
     * @return \Swis\JsonApi\Errors\ErrorCollection
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
