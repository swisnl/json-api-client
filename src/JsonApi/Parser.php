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
     * Parser constructor.
     *
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
     * @param $jsonApiDocument
     *
     * @return \Swis\JsonApi\Interfaces\DocumentInterface
     */
    protected function buildDataDocument($jsonApiDocument): DocumentInterface
    {
        $data = $this->getJsonApiDocumentData($jsonApiDocument);
        $includedInDocument = $this->getJsonApiDocumentIncluded($jsonApiDocument);

        $included = null;
        if ($includedInDocument) {
            // @todo: de collection hydrator zo slim maken dat 'ie eerst de items doet, en daarna de relationships. Dan hoeven we niet eerst (zoals nu) de items om te zetten naar een collection
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
     * @param $jsonApiDocument
     *
     * @return \Art4\JsonApiClient\Resource\ResourceInterface
     */
    private function getJsonApiDocumentData($jsonApiDocument): JsonApiResourceInterface
    {
        /** @var JsonApiResourceInterface $data */
        $resource = $jsonApiDocument->get('data');

        if (!$resource instanceof JsonApiResourceInterface) {
            throw new \DomainException('Result is not a Json API Resource');
        }

        return $resource;
    }

    /**
     * @param $jsonApiDocument
     *
     * @return \Art4\JsonApiClient\Resource\Collection|null
     */
    private function getJsonApiDocumentIncluded($jsonApiDocument)
    {
        if ($jsonApiDocument->has('included')) {
            return $jsonApiDocument->get('included');
        }

        return null;
    }

    /**
     * @param JsonApiDocumentInterface $document
     *
     * @return array
     */
    private function parseLinks(\Art4\JsonApiClient\DocumentInterface $document): array
    {
        if (!$document->has('links')) {
            return [];
        }

        return $document->get('links')->asArray(true);
    }

    /**
     * @param JsonApiDocumentInterface $document
     *
     * @return \Swis\JsonApi\Errors\ErrorCollection
     */
    private function parseErrors(\Art4\JsonApiClient\DocumentInterface $document): ErrorCollection
    {
        if (!$document->has('errors')) {
            return new ErrorCollection();
        }

        return $this->errorsParser->parse($document->get('errors'));
    }

    /**
     * @param JsonApiDocumentInterface $document
     *
     * @return array
     */
    private function parseMeta(\Art4\JsonApiClient\DocumentInterface $document): array
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
