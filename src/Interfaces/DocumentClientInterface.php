<?php

namespace Swis\JsonApi\Interfaces;

interface DocumentClientInterface
{
    /**
     * @param string $endpoint
     *
     * @return \Swis\JsonApi\Interfaces\DocumentInterface
     */
    public function get(string $endpoint): DocumentInterface;

    /**
     * @param string                                         $endpoint
     * @param \Swis\JsonApi\Interfaces\ItemDocumentInterface $document
     *
     * @return \Swis\JsonApi\Interfaces\DocumentInterface
     */
    public function patch(string $endpoint, ItemDocumentInterface $document): DocumentInterface;

    /**
     * @param string                                         $endpoint
     * @param \Swis\JsonApi\Interfaces\ItemDocumentInterface $document
     *
     * @return \Swis\JsonApi\Interfaces\DocumentInterface
     */
    public function post(string $endpoint, ItemDocumentInterface $document): DocumentInterface;

    /**
     * @param string $endpoint
     *
     * @return \Swis\JsonApi\Interfaces\DocumentInterface
     */
    public function delete(string $endpoint): DocumentInterface;

    /**
     * @return string
     */
    public function getBaseUri(): string;

    /**
     * @param string $baseUri
     */
    public function setBaseUri(string $baseUri);
}
