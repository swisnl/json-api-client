<?php

namespace Swis\JsonApi\JsonApi;

use Art4\JsonApiClient\DocumentInterface as JsonApiDocumentInterface;
use Art4\JsonApiClient\Resource\ResourceInterface as JsonApiResourceInterface;
use Art4\JsonApiClient\Utils\Manager as Art4JsonApiClientManager;
use Swis\JsonApi\CollectionDocument;
use Swis\JsonApi\Document;
use Swis\JsonApi\Errors\ErrorCollection;
use Swis\JsonApi\Interfaces\DocumentInterface;
use Swis\JsonApi\Interfaces\ParserInterface;
use Swis\JsonApi\ItemDocument;

class Parser implements ParserInterface
{
    /**
     * @var \Art4\JsonApiClient\Utils\Manager
     */
    protected $manager;

    /**
     * @var \Swis\JsonApi\JsonApi\Hydrator
     */
    private $hydrator;

    /**
     * @var \Swis\JsonApi\JsonApi\ErrorsParser
     */
    private $errorsParser;

    /**
     * @param \Art4\JsonApiClient\Utils\Manager  $manager
     * @param \Swis\JsonApi\JsonApi\Hydrator     $hydrator
     * @param \Swis\JsonApi\JsonApi\ErrorsParser $errorsParser
     */
    public function __construct(Art4JsonApiClientManager $manager, Hydrator $hydrator, ErrorsParser $errorsParser)
    {
        $this->manager = $manager;
        $this->hydrator = $hydrator;
        $this->errorsParser = $errorsParser;
    }

    /**
     * @return \Swis\JsonApi\JsonApi\Hydrator
     */
    public function getHydrator(): Hydrator
    {
        return $this->hydrator;
    }

    /**
     * @param string $json
     *
     * @throws \DomainException
     *
     * @return \Swis\JsonApi\Interfaces\DocumentInterface
     */
    public function deserialize(string $json): DocumentInterface
    {
        $jsonApiDocument = $this->getJsonApiDocument($json);

        if ($jsonApiDocument->has('data')) {
            $document = $this->buildDataDocument($jsonApiDocument);
        } else {
            $document = new Document();
        }

        $document->setLinks($this->parseLinks($jsonApiDocument));
        $document->setErrors($this->parseErrors($jsonApiDocument));
        $document->setMeta($this->parseMeta($jsonApiDocument));

        return $document;
    }

    /**
     * @param string $json
     *
     * @throws \DomainException
     *
     * @return \Art4\JsonApiClient\DocumentInterface
     */
    private function getJsonApiDocument(string $json): JsonApiDocumentInterface
    {
        /** @var \Art4\JsonApiClient\DocumentInterface $jsonApiDocument */
        $jsonApiDocument = $this->manager->parse($json);

        if (!$jsonApiDocument instanceof JsonApiDocumentInterface) {
            throw new \DomainException('Result is not a JSON API Document');
        }

        return $jsonApiDocument;
    }

    /**
     * @param \Art4\JsonApiClient\DocumentInterface $jsonApiDocument
     *
     * @throws \DomainException
     *
     * @return \Swis\JsonApi\Interfaces\DocumentInterface
     */
    protected function buildDataDocument(JsonApiDocumentInterface $jsonApiDocument): DocumentInterface
    {
        $data = $this->getJsonApiDocumentData($jsonApiDocument);
        $includedInDocument = $this->getJsonApiDocumentIncluded($jsonApiDocument);

        $included = null;
        if ($includedInDocument) {
            // @TODO: Make the collection hydrator smarter so that it first creates the items and then the relationships. In that way, we don't have to convert the items to a collection like we have to now.
            $included = $this->hydrator->hydrateCollection($includedInDocument, $this->hydrator->hydrateCollection($includedInDocument));
        }

        if ($data->isCollection()) {
            $document = new CollectionDocument();
            $document->setData(
                $this->hydrator->hydrateCollection($jsonApiDocument->get('data'), $included)
            );
        } elseif ($data->isItem()) {
            $document = new ItemDocument();
            $document->setData(
                $this->hydrator->hydrateItem($jsonApiDocument->get('data'), $included)
            );
        } else {
            throw new \DomainException('Data is not Collection or Item');
        }

        if ($included) {
            $document->setIncluded($included);

            return $document;
        }

        return $document;
    }

    /**
     * @param \Art4\JsonApiClient\DocumentInterface $document
     *
     * @throws \DomainException
     *
     * @return \Art4\JsonApiClient\Resource\ResourceInterface
     */
    private function getJsonApiDocumentData(JsonApiDocumentInterface $document): JsonApiResourceInterface
    {
        /** @var \Art4\JsonApiClient\Resource\ResourceInterface $resource */
        $resource = $document->get('data');

        if (!$resource instanceof JsonApiResourceInterface) {
            throw new \DomainException('Result is not a Json API Resource');
        }

        return $resource;
    }

    /**
     * @param \Art4\JsonApiClient\DocumentInterface $document
     *
     * @return \Art4\JsonApiClient\Resource\Collection|null
     */
    private function getJsonApiDocumentIncluded(JsonApiDocumentInterface $document)
    {
        if ($document->has('included')) {
            return $document->get('included');
        }

        return null;
    }

    /**
     * @param \Art4\JsonApiClient\DocumentInterface $document
     *
     * @return array
     */
    private function parseLinks(JsonApiDocumentInterface $document): array
    {
        if (!$document->has('links')) {
            return [];
        }

        return $document->get('links')->asArray(true);
    }

    /**
     * @param \Art4\JsonApiClient\DocumentInterface $document
     *
     * @return \Swis\JsonApi\Errors\ErrorCollection
     */
    private function parseErrors(JsonApiDocumentInterface $document): ErrorCollection
    {
        if (!$document->has('errors')) {
            return new ErrorCollection();
        }

        return $this->errorsParser->parse($document->get('errors'));
    }

    /**
     * @param \Art4\JsonApiClient\DocumentInterface $document
     *
     * @return array
     */
    private function parseMeta(JsonApiDocumentInterface $document): array
    {
        if (!$document->has('meta')) {
            return [];
        }

        return $document->get('meta')->asArray(true);
    }

    /**
     * @param \Swis\JsonApi\Interfaces\DocumentInterface $document
     *
     * @return string
     */
    public function serialize(DocumentInterface $document): string
    {
    }
}
