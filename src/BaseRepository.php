<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client;

use Swis\JsonApi\Client\Interfaces\DocumentClientInterface;

abstract class BaseRepository
{
    /**
     * @var \Swis\JsonApi\Client\Interfaces\DocumentClientInterface
     */
    protected $client;

    /**
     * @var \Swis\JsonApi\Client\DocumentFactory
     */
    protected $documentFactory;

    /**
     * @var string
     */
    protected $endpoint = '';

    public function __construct(DocumentClientInterface $client, DocumentFactory $documentFactory)
    {
        $this->client = $client;
        $this->documentFactory = $documentFactory;
    }

    /**
     * @return \Swis\JsonApi\Client\Interfaces\DocumentClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }
}
