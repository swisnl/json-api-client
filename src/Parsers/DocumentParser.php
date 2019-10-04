<?php

namespace Swis\JsonApi\Client\Parsers;

use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\CollectionDocument;
use Swis\JsonApi\Client\Document;
use Swis\JsonApi\Client\Exceptions\ValidationException;
use Swis\JsonApi\Client\Interfaces\DocumentInterface;
use Swis\JsonApi\Client\Interfaces\DocumentParserInterface;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Interfaces\ManyRelationInterface;
use Swis\JsonApi\Client\Interfaces\OneRelationInterface;
use Swis\JsonApi\Client\ItemDocument;

class DocumentParser implements DocumentParserInterface
{
    /**
     * @var \Swis\JsonApi\Client\Parsers\ItemParser
     */
    private $itemParser;

    /**
     * @var \Swis\JsonApi\Client\Parsers\CollectionParser
     */
    private $collectionParser;

    /**
     * @var \Swis\JsonApi\Client\Parsers\ErrorCollectionParser
     */
    private $errorCollectionParser;

    /**
     * @var \Swis\JsonApi\Client\Parsers\LinksParser
     */
    private $linksParser;

    /**
     * @var \Swis\JsonApi\Client\Parsers\JsonapiParser
     */
    private $jsonapiParser;

    /**
     * @var \Swis\JsonApi\Client\Parsers\MetaParser
     */
    private $metaParser;

    /**
     * @param \Swis\JsonApi\Client\Parsers\ItemParser            $itemParser
     * @param \Swis\JsonApi\Client\Parsers\CollectionParser      $collectionParser
     * @param \Swis\JsonApi\Client\Parsers\ErrorCollectionParser $errorCollectionParser
     * @param \Swis\JsonApi\Client\Parsers\LinksParser           $linksParser
     * @param \Swis\JsonApi\Client\Parsers\JsonapiParser         $jsonapiParser
     * @param \Swis\JsonApi\Client\Parsers\MetaParser            $metaParser
     */
    public function __construct(
        ItemParser $itemParser,
        CollectionParser $collectionParser,
        ErrorCollectionParser $errorCollectionParser,
        LinksParser $linksParser,
        JsonapiParser $jsonapiParser,
        MetaParser $metaParser
    ) {
        $this->itemParser = $itemParser;
        $this->collectionParser = $collectionParser;
        $this->errorCollectionParser = $errorCollectionParser;
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
        $data = $this->decodeJson($json);

        if (!is_object($data)) {
            throw new ValidationException(sprintf('Document MUST be an object, "%s" given.', gettype($data)));
        }
        if (!property_exists($data, 'data') && !property_exists($data, 'errors') && !property_exists($data, 'meta')) {
            throw new ValidationException('Document MUST contain at least one of the following properties: `data`, `errors`, `meta`.');
        }
        if (property_exists($data, 'data') && property_exists($data, 'errors')) {
            throw new ValidationException('The properties `data` and `errors` MUST NOT coexist in Document.');
        }
        if (!property_exists($data, 'data') && property_exists($data, 'included')) {
            throw new ValidationException('If Document does not contain a `data` property, the `included` property MUST NOT be present either.');
        }
        if (property_exists($data, 'data') && !is_object($data->data) && !is_array($data->data) && $data->data !== null) {
            throw new ValidationException(sprintf('Document property "data" MUST be null, an array or an object, "%s" given.', gettype($data->data)));
        }
        if (property_exists($data, 'included') && !is_array($data->included)) {
            throw new ValidationException(sprintf('Document property "included" MUST be an array, "%s" given.', gettype($data->included)));
        }

        $document = $this->getDocument($data);

        if (property_exists($data, 'links')) {
            $document->setLinks($this->linksParser->parse($data->links, LinksParser::SOURCE_DOCUMENT));
        }

        if (property_exists($data, 'errors')) {
            $document->setErrors($this->errorCollectionParser->parse($data->errors));
        }

        if (property_exists($data, 'meta')) {
            $document->setMeta($this->metaParser->parse($data->meta));
        }

        if (property_exists($data, 'jsonapi')) {
            $document->setJsonapi($this->jsonapiParser->parse($data->jsonapi));
        }

        return $document;
    }

    /**
     * @param string $json
     *
     * @return mixed
     */
    private function decodeJson(string $json)
    {
        $data = json_decode($json, false);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ValidationException(sprintf('Unable to parse JSON data: %s', json_last_error_msg()), json_last_error());
        }

        return $data;
    }

    /**
     * @param mixed $data
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    private function getDocument($data): DocumentInterface
    {
        if (!property_exists($data, 'data') || $data->data === null) {
            return new Document();
        }

        if (is_array($data->data)) {
            $document = (new CollectionDocument())
                ->setData($this->collectionParser->parse($data->data));
        } else {
            $document = (new ItemDocument())
                ->setData($this->itemParser->parse($data->data));
        }

        if (property_exists($data, 'included')) {
            $document->setIncluded($this->collectionParser->parse($data->included));
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
                        /** @var \Swis\JsonApi\Client\Interfaces\ItemInterface|null $relatedItem */
                        $relatedItem = $relation->getIncluded();

                        if ($relatedItem === null) {
                            continue;
                        }

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
}
