<?php

namespace Swis\JsonApi\Client\JsonApi;

use Art4\JsonApiClient\DocumentInterface as Art4JsonApiDocumentInterface;
use Art4\JsonApiClient\ResourceCollectionInterface;
use Art4\JsonApiClient\ResourceItemInterface;
use Art4\JsonApiClient\Utils\Manager as Art4JsonApiClientManager;
use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\CollectionDocument;
use Swis\JsonApi\Client\Document;
use Swis\JsonApi\Client\Errors\ErrorCollection;
use Swis\JsonApi\Client\Interfaces\DocumentInterface;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use Swis\JsonApi\Client\ItemDocument;

class Parser implements ParserInterface
{
    /**
     * @var \Art4\JsonApiClient\Utils\Manager
     */
    protected $manager;

    /**
     * @var \Swis\JsonApi\Client\JsonApi\Hydrator
     */
    private $hydrator;

    /**
     * @var \Swis\JsonApi\Client\JsonApi\ErrorsParser
     */
    private $errorsParser;

    /**
     * @param \Art4\JsonApiClient\Utils\Manager         $manager
     * @param \Swis\JsonApi\Client\JsonApi\Hydrator     $hydrator
     * @param \Swis\JsonApi\Client\JsonApi\ErrorsParser $errorsParser
     */
    public function __construct(Art4JsonApiClientManager $manager, Hydrator $hydrator, ErrorsParser $errorsParser)
    {
        $this->manager = $manager;
        $this->hydrator = $hydrator;
        $this->errorsParser = $errorsParser;
    }

    /**
     * @return \Swis\JsonApi\Client\JsonApi\Hydrator
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
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
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
    private function getJsonApiDocument(string $json): Art4JsonApiDocumentInterface
    {
        $jsonApiDocument = $this->manager->parse($json);

        if (!$jsonApiDocument instanceof Art4JsonApiDocumentInterface) {
            throw new \DomainException('Result is not a JSON API Document');
        }

        return $jsonApiDocument;
    }

    /**
     * @param \Art4\JsonApiClient\DocumentInterface $jsonApiDocument
     *
     * @throws \DomainException
     *includedInDocument
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    protected function buildDataDocument(Art4JsonApiDocumentInterface $jsonApiDocument): DocumentInterface
    {
        $data = $this->getJsonApiDocumentData($jsonApiDocument);
        $includedInDocument = $this->getJsonApiDocumentIncluded($jsonApiDocument);
        $relationshipsInDocument = $this->getJsonApiDocumentRelationShips($jsonApiDocument);
        $allHydratedItems = new Collection();
        $allJsonApiItems = new Collection();

        if ($data instanceof ResourceItemInterface) {
            $item = $this->hydrator->hydrateItem($data);
            $allHydratedItems->push($item);
            $allJsonApiItems->push($data);

            $document = new ItemDocument();
            $document->setData($item);
        } elseif ($data instanceof ResourceCollectionInterface) {
            $collection = $this->hydrator->hydrateCollection($data);
            $allHydratedItems = $allHydratedItems->concat($collection);
            $allJsonApiItems = $allJsonApiItems->concat(new Collection($data->asArray()));

            $document = new CollectionDocument();
            $document->setData($collection);
        } else {
            throw new \DomainException('Data is not Collection or Item');
        }

        $included = null;
        if ($includedInDocument) {
            $included = $this->hydrator->hydrateCollection($includedInDocument);
            $allHydratedItems = $allHydratedItems->concat($included);
            $allJsonApiItems = $allJsonApiItems->concat(new Collection($includedInDocument->asArray()));
        }

        if ($relationshipsInDocument) {
            $relationships = $this->hydrator->hydrateRelationCollection($relationshipsInDocument);
            if ($includedInDocument) {
                $newRelationships = new Collection();
                foreach ($relationships as $relationship) {
                    $id = $relationship->getId();
                    $type = $relationship->getType();
                    $desiredObject = null;

                    //check duplicate
                    $desiredObject = $included->first(function ($item) use ($id, $type) {
                        return ($item->getId() == $id) && ($item->getType() == $type);
                    });
                    if (is_null($desiredObject)) {
                        $newRelationships->push($relationship);
                    }
                }
            } else {
                $newRelationships = $relationships;
            }
        }

        //$allHydratedItems Items will be in response
        $this->hydrator->hydrateRelationships($allJsonApiItems, $allHydratedItems);
        if ($included) {
            $document->setIncluded($included);
        }

        return $document;
    }

    /**
     * @param \Art4\JsonApiClient\DocumentInterface $document
     *
     * @throws \DomainException
     *
     * @return \Art4\JsonApiClient\ResourceItemInterface|\Art4\JsonApiClient\ResourceCollectionInterface
     */
    private function getJsonApiDocumentData(Art4JsonApiDocumentInterface $document)
    {
        $resource = $document->get('data');

        if (!$resource instanceof ResourceItemInterface && !$resource instanceof ResourceCollectionInterface) {
            throw new \DomainException('Result is not a Json API Resource');
        }

        return $resource;
    }

    /**
     * @param \Art4\JsonApiClient\DocumentInterface $document
     *
     * @return \Art4\JsonApiClient\ResourceCollection|null
     */
    private function getJsonApiDocumentIncluded(Art4JsonApiDocumentInterface $document)
    {
        if ($document->has('included')) {
            return $document->get('included');
        }

        return null;
    }

    /**
     * @param \Art4\JsonApiClient\DocumentInterface $document
     *
     * @return \Art4\JsonApiClient\ResourceCollection|null
     */
    private function getJsonApiDocumentRelationShips(Art4JsonApiDocumentInterface $document)
    {
        if ($document->has('data.relationships')) {
            return $document->get('data.relationships');
        }

        return null;
    }

    /**
     * @param \Art4\JsonApiClient\DocumentInterface $document
     *
     * @return array
     */
    private function parseLinks(Art4JsonApiDocumentInterface $document): array
    {
        if (!$document->has('links')) {
            return [];
        }

        return $document->get('links')->asArray(true);
    }

    /**
     * @param \Art4\JsonApiClient\DocumentInterface $document
     *
     * @return \Swis\JsonApi\Client\Errors\ErrorCollection
     */
    private function parseErrors(Art4JsonApiDocumentInterface $document): ErrorCollection
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
    private function parseMeta(Art4JsonApiDocumentInterface $document): array
    {
        if (!$document->has('meta')) {
            return [];
        }

        return $document->get('meta')->asArray(true);
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\DocumentInterface $document
     *
     * @return string
     */
    public function serialize(DocumentInterface $document): string
    {
    }
}
