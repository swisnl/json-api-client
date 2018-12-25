<?php

namespace Swis\JsonApi\Client\Interfaces;

interface DocumentClientInterface
{
    /**
     * @param string $endpoint
     * @param array  $headers
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function get(string $endpoint, array $headers = []): DocumentInterface;

    /**
     * @param string                                                $endpoint
     * @param \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface $document
     * @param array                                                 $headers
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function patch(string $endpoint, ItemDocumentInterface $document, array $headers = []): DocumentInterface;

    /**
     * @param string                                                $endpoint
     * @param \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface $document
     * @param array                                                 $headers
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function post(string $endpoint, ItemDocumentInterface $document, array $headers = []): DocumentInterface;

    /**
     * @param string $endpoint
     * @param array  $headers
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function delete(string $endpoint, array $headers = []): DocumentInterface;

    /**
     * @return string
     */
    public function getBaseUri(): string;

    /**
     * @param string $baseUri
     */
    public function setBaseUri(string $baseUri);
}
