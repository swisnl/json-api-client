<?php

namespace Swis\JsonApi\Client;

use Swis\JsonApi\Client\Exceptions\DocumentNotFoundException;
use Swis\JsonApi\Client\Exceptions\DocumentTypeException;
use Swis\JsonApi\Client\Interfaces\CollectionDocumentInterface;
use Swis\JsonApi\Client\Interfaces\DocumentClientInterface;
use Swis\JsonApi\Client\Interfaces\ItemDocumentInterface;
use Swis\JsonApi\Client\Interfaces\RepositoryInterface;

class Repository implements RepositoryInterface
{
    /**
     * @var \Swis\JsonApi\Client\Interfaces\DocumentClientInterface
     */
    protected $client;

    /**
     * @var string
     */
    protected $endpoint = '';

    /**
     * @param \Swis\JsonApi\Client\Interfaces\DocumentClientInterface $client
     */
    public function __construct(DocumentClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $parameters
     *
     * @throws \Swis\JsonApi\Client\Exceptions\DocumentTypeException
     *
     * @return \Swis\JsonApi\Client\Interfaces\CollectionDocumentInterface
     */
    public function all(array $parameters = [])
    {
        $document = $this->getClient()->get($this->getEndpoint().'?'.http_build_query($parameters));

        if (!$document instanceof CollectionDocumentInterface) {
            throw new DocumentTypeException(
                sprintf('Expected %s got %s', CollectionDocumentInterface::class, get_class($document)),
                $document
            );
        }

        return $document;
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
     * @param       $id
     * @param array $parameters
     *
     * @throws \Swis\JsonApi\Client\Exceptions\DocumentNotFoundException
     * @throws \Swis\JsonApi\Client\Exceptions\DocumentTypeException
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface
     */
    public function find($id, array $parameters = [])
    {
        $document = $this->getClient()->get($this->getEndpoint().'/'.urlencode($id).'?'.http_build_query($parameters));

        if (null === $document->getData()) {
            throw new DocumentNotFoundException();
        }
        if (!$document instanceof ItemDocumentInterface) {
            throw new DocumentTypeException(
                sprintf('Expected %s got %s', ItemDocumentInterface::class, get_class($document)),
                $document
            );
        }

        return $document;
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface $document
     * @param array                                                 $parameters
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function save(ItemDocumentInterface $document, array $parameters = [])
    {
        if ($document->getData()->isNew()) {
            return $this->saveNew($document, $parameters);
        } else {
            return $this->saveExisting($document, $parameters);
        }
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface $document
     * @param array                                                 $parameters
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    protected function saveNew(ItemDocumentInterface $document, array $parameters = [])
    {
        return $this->getClient()->post($this->getEndpoint().'?'.http_build_query($parameters), $document);
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface $document
     * @param array                                                 $parameters
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    protected function saveExisting(ItemDocumentInterface $document, array $parameters = [])
    {
        return $this->getClient()->patch(
            $this->getEndpoint().'/'.urlencode($document->getData()->getId()).'?'.http_build_query($parameters),
            $document
        );
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface $document
     * @param array                                                 $parameters
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function delete(ItemDocumentInterface $document, array $parameters = [])
    {
        return $this->deleteById($document->getData()->getId(), $parameters);
    }

    /**
     * @param       $id
     * @param array $parameters
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function deleteById($id, array $parameters = [])
    {
        return $this->getClient()->delete($this->getEndpoint().'/'.urlencode($id).'?'.http_build_query($parameters));
    }
}
