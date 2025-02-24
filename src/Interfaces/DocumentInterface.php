<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

use Psr\Http\Message\ResponseInterface;
use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\ErrorCollection;
use Swis\JsonApi\Client\Jsonapi;
use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;

interface DocumentInterface extends \JsonSerializable
{
    public function getResponse(): ?ResponseInterface;

    /**
     * @return $this
     */
    public function setResponse(?ResponseInterface $response);

    /**
     * @return \Swis\JsonApi\Client\Interfaces\DataInterface
     */
    public function getData();

    /**
     * @return $this
     */
    public function setData(DataInterface $data);

    public function getErrors(): ErrorCollection;

    /**
     * @return $this
     */
    public function setErrors(ErrorCollection $errors);

    public function hasErrors(): bool;

    public function getMeta(): ?Meta;

    /**
     * @return $this
     */
    public function setMeta(?Meta $meta);

    public function getLinks(): ?Links;

    /**
     * @return $this
     */
    public function setLinks(?Links $links);

    /**
     * @return mixed
     */
    public function getIncluded(): Collection;

    /**
     * @return $this
     */
    public function setIncluded(Collection $included);

    public function getJsonapi(): ?Jsonapi;

    /**
     * @return $this
     */
    public function setJsonapi(?Jsonapi $jsonapi);

    public function toArray(): array;
}
