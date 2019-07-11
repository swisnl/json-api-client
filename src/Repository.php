<?php

namespace Swis\JsonApi\Client;

use Swis\JsonApi\Client\Interfaces\DocumentClientInterface;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Interfaces\RepositoryInterface;

class Repository implements RepositoryInterface
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

    /**
     * @param \Swis\JsonApi\Client\Interfaces\DocumentClientInterface $client
     * @param \Swis\JsonApi\Client\DocumentFactory                    $documentFactory
     */
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

    /**
     * @param array $parameters
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function all(array $parameters = [])
    {
        return $this->getClient()->get($this->getEndpoint().'?'.http_build_query($parameters));
    }

    /**
     * @param array $parameters
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function take(array $parameters = [])
    {
        return $this->getClient()->get($this->getEndpoint().'?'.http_build_query($parameters));
    }

    /**
     * @param string $id
     * @param array  $parameters
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function find(string $id, array $parameters = [])
    {
        return $this->getClient()->get($this->getEndpoint().'/'.urlencode($id).'?'.http_build_query($parameters));
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     * @param array                                         $parameters
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function save(ItemInterface $item, array $parameters = [])
    {
        if ($item->isNew()) {
            return $this->saveNew($item, $parameters);
        }

        return $this->saveExisting($item, $parameters);
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     * @param array                                         $parameters
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    protected function saveNew(ItemInterface $item, array $parameters = [])
    {
        return $this->getClient()->post(
            $this->getEndpoint().'?'.http_build_query($parameters),
            $this->documentFactory->make($item)
        );
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     * @param array                                         $parameters
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    protected function saveExisting(ItemInterface $item, array $parameters = [])
    {
        return $this->getClient()->patch(
            $this->getEndpoint().'/'.urlencode($item->getId()).'?'.http_build_query($parameters),
            $this->documentFactory->make($item)
        );
    }

    /**
     * @param string $id
     * @param array  $parameters
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function delete(string $id, array $parameters = [])
    {
        return $this->getClient()->delete($this->getEndpoint().'/'.urlencode($id).'?'.http_build_query($parameters));
    }
}
