<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

interface DocumentClientInterface
{
    public function get(string $endpoint, array $headers = []): DocumentInterface;

    public function patch(string $endpoint, ItemDocumentInterface $document, array $headers = []): DocumentInterface;

    public function post(string $endpoint, ItemDocumentInterface $document, array $headers = []): DocumentInterface;

    public function delete(string $endpoint, array $headers = []): DocumentInterface;

    public function getBaseUri(): string;

    public function setBaseUri(string $baseUri);
}
