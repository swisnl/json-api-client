<?php

namespace Swis\JsonApi\Client\JsonApi;

use Art4\JsonApiClient\DocumentInterface as Art4JsonApiDocumentInterface;
use Art4\JsonApiClient\ResourceCollectionInterface;
use Art4\JsonApiClient\ResourceItemInterface;
use Art4\JsonApiClient\Utils\Manager as Art4JsonApiClientManager;
use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\CollectionDocument;
use Swis\JsonApi\Client\Document;
use Swis\JsonApi\Client\ErrorCollection;
use Swis\JsonApi\Client\Interfaces\DocumentInterface;
use Swis\JsonApi\Client\Interfaces\DocumentParserInterface;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Interfaces\ManyRelationInterface;
use Swis\JsonApi\Client\Interfaces\OneRelationInterface;
use Swis\JsonApi\Client\ItemDocument;
use Swis\JsonApi\Client\Jsonapi;
use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;

class DocumentParser implements DocumentParserInterface
{
    /**
     * @var \Art4\JsonApiClient\Utils\Manager
     */
    private $manager;

    /**
     * @var \Swis\JsonApi\Client\JsonApi\ItemParser
     */
    private $itemParser;

    /**
     * @var \Swis\JsonApi\Client\JsonApi\CollectionParser
     */
    private $collectionParser;

    /**
     * @var \Swis\JsonApi\Client\JsonApi\ErrorsParser
     */
    private $errorsParser;

    /**
     * @var \Swis\JsonApi\Client\JsonApi\LinksParser
     */
    private $linksParser;

    /**
     * @var \Swis\JsonApi\Client\JsonApi\JsonapiParser
     */
    private $jsonapiParser;

    /**
     * @var \Swis\JsonApi\Client\JsonApi\MetaParser
     */
    private $metaParser;

    /**
     * @param \Art4\JsonApiClient\Utils\Manager             $manager
     * @param \Swis\JsonApi\Client\JsonApi\ItemParser       $itemParser
     * @param \Swis\JsonApi\Client\JsonApi\CollectionParser $collectionParser
     * @param \Swis\JsonApi\Client\JsonApi\ErrorsParser     $errorsParser
     * @param \Swis\JsonApi\Client\JsonApi\LinksParser      $linksParser
     * @param \Swis\JsonApi\Client\JsonApi\JsonapiParser    $jsonapiParser
     * @param \Swis\JsonApi\Client\JsonApi\MetaParser       $metaParser
     */
    public function __construct(
        Art4JsonApiClientManager $manager,
        ItemParser $itemParser,
        CollectionParser $collectionParser,
        ErrorsParser $errorsParser,
        LinksParser $linksParser,
        JsonapiParser $jsonapiParser,
        MetaParser $metaParser
    ) {
        $this->manager = $manager;
        $this->itemParser = $itemParser;
        $this->collectionParser = $collectionParser;
        $this->errorsParser = $errorsParser;
        $this->linksParser = $linksParser;
        $this->jsonapiParser = $jsonapiParser;
        $this->metaParser = $metaParser;
    }

    /**
     * @param string $json
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function parse(string $json): DocumentInterface
    {
        /** @var \Art4\JsonApiClient\DocumentInterface $jsonApiDocument */
        $jsonApiDocument = $this->manager->parse($json);

        return $this->getDocument($jsonApiDocument)
            ->setLinks($this->parseLinks($jsonApiDocument))
            ->setErrors($this->parseErrors($jsonApiDocument))
            ->setMeta($this->parseMeta($jsonApiDocument))
            ->setJsonapi($this->parseJsonapi($jsonApiDocument));
    }

    /**
     * @param \Art4\JsonApiClient\DocumentInterface $jsonApiDocument
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    private function getDocument(Art4JsonApiDocumentInterface $jsonApiDocument): DocumentInterface
    {
        if (!$jsonApiDocument->has('data')) {
            return new Document();
        }

        $data = $jsonApiDocument->get('data');

        if ($data instanceof ResourceItemInterface) {
            $document = (new ItemDocument())
                ->setData($this->itemParser->parse($data));
        } elseif ($data instanceof ResourceCollectionInterface) {
            $document = (new CollectionDocument())
                ->setData($this->collectionParser->parse($data));
        } else {
            throw new \DomainException('Document data is not a Collection or an Item');
        }

        if ($jsonApiDocument->has('included')) {
            $document->setIncluded($this->collectionParser->parse($jsonApiDocument->get('included')));
        }

        $allItems = Collection::wrap($document->getData())
            ->concat($document->getIncluded());

        $this->linkRelationships($allItems);

        return $document;
    }

    /**
     * @param \Swis\JsonApi\Client\Collection $items
     */
    private function linkRelationships(Collection $items): void
    {
        // N.B. We reverse the items to make sure the first item in the collection takes precedence
        $keyedItems = $items->reverse()->keyBy(
            function (ItemInterface $item) {
                return $this->getItemKey($item);
            }
        );

        $items->each(
            function (ItemInterface $item) use ($keyedItems) {
                foreach ($item->getRelations() as $name => $relation) {
                    if ($relation instanceof OneRelationInterface) {
                        /** @var \Swis\JsonApi\Client\Interfaces\ItemInterface $relatedItem */
                        $relatedItem = $relation->getIncluded();

                        $includedItem = $this->getItem($keyedItems, $relatedItem);
                        if ($includedItem !== null) {
                            $relation->associate($includedItem);
                        }
                    } elseif ($relation instanceof ManyRelationInterface) {
                        /** @var \Swis\JsonApi\Client\Collection $relatedCollection */
                        $relatedCollection = $relation->getIncluded();

                        /** @var \Swis\JsonApi\Client\Interfaces\ItemInterface $relatedItem */
                        foreach ($relatedCollection as $key => $relatedItem) {
                            $includedItem = $this->getItem($keyedItems, $relatedItem);
                            if ($includedItem !== null) {
                                $relatedCollection->put($key, $includedItem);
                            }
                        }
                    }
                }
            }
        );
    }

    /**
     * @param \Swis\JsonApi\Client\Collection               $included
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface|null
     */
    private function getItem(Collection $included, ItemInterface $item): ?ItemInterface
    {
        return $included->get($this->getItemKey($item));
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     *
     * @return string
     */
    private function getItemKey(ItemInterface $item): string
    {
        return sprintf('%s:%s', $item->getType(), $item->getId());
    }

    /**
     * @param \Art4\JsonApiClient\DocumentInterface $document
     *
     * @return \Swis\JsonApi\Client\Links|null
     */
    private function parseLinks(Art4JsonApiDocumentInterface $document): ?Links
    {
        if (!$document->has('links')) {
            return null;
        }

        return $this->linksParser->parse($document->get('links')->asArray());
    }

    /**
     * @param \Art4\JsonApiClient\DocumentInterface $document
     *
     * @return \Swis\JsonApi\Client\ErrorCollection
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
     * @return \Swis\JsonApi\Client\Meta|null
     */
    private function parseMeta(Art4JsonApiDocumentInterface $document): ?Meta
    {
        if (!$document->has('meta')) {
            return null;
        }

        return $this->metaParser->parse($document->get('meta'));
    }

    /**
     * @param \Art4\JsonApiClient\DocumentInterface $document
     *
     * @return \Swis\JsonApi\Client\Jsonapi|null
     */
    private function parseJsonapi(Art4JsonApiDocumentInterface $document): ?Jsonapi
    {
        if (!$document->has('jsonapi')) {
            return null;
        }

        return $this->jsonapiParser->parse($document->get('jsonapi'));
    }
}
