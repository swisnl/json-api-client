<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Parsers;

use JsonException;
use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\CollectionDocument;
use Swis\JsonApi\Client\Document;
use Swis\JsonApi\Client\Exceptions\ValidationException;
use Swis\JsonApi\Client\Interfaces\DocumentInterface;
use Swis\JsonApi\Client\Interfaces\DocumentParserInterface;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Interfaces\ManyRelationInterface;
use Swis\JsonApi\Client\Interfaces\OneRelationInterface;
use Swis\JsonApi\Client\Interfaces\TypeMapperInterface;
use Swis\JsonApi\Client\ItemDocument;
use Swis\JsonApi\Client\TypeMapper;

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
     * @param \Swis\JsonApi\Client\Interfaces\TypeMapperInterface|null $typeMapper
     *
     * @return static
     */
    public static function create(TypeMapperInterface $typeMapper = null): self
    {
        $metaParser = new MetaParser();
        $linksParser = new LinksParser($metaParser);
        $itemParser = new ItemParser($typeMapper ?? new TypeMapper(), $linksParser, $metaParser);

        return new static(
            $itemParser,
            new CollectionParser($itemParser),
            new ErrorCollectionParser(
                new ErrorParser($linksParser, $metaParser)
            ),
            $linksParser,
            new JsonapiParser($metaParser),
            $metaParser
        );
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
        try {
            return json_decode($json, false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new ValidationException(sprintf('Unable to parse JSON data: %s', $exception->getMessage()), 0, $exception);
        }
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

        $duplicateItems = $this->getDuplicateItems($allItems);

        if ($duplicateItems->isNotEmpty()) {
            throw new ValidationException(sprintf('Resources MUST be unique based on their `type` and `id`, %d duplicate(s) found.', $duplicateItems->count()));
        }

        $this->linkRelationships($allItems);

        return $document;
    }

    /**
     * @param \Swis\JsonApi\Client\Collection $items
     */
    private function linkRelationships(Collection $items): void
    {
        $keyedItems = $items->keyBy(fn (ItemInterface $item) => $this->getItemKey($item));

        $items->each(
            function (ItemInterface $item) use ($keyedItems) {
                foreach ($item->getRelations() as $name => $relation) {
                    if ($relation instanceof OneRelationInterface) {
                        /** @var \Swis\JsonApi\Client\Interfaces\ItemInterface|null $relatedItem */
                        $relatedItem = $relation->getData();

                        if ($relatedItem === null) {
                            continue;
                        }

                        $includedItem = $this->getItem($keyedItems, $relatedItem);
                        if ($includedItem !== null) {
                            $relation->setIncluded($includedItem);
                        }
                    } elseif ($relation instanceof ManyRelationInterface) {
                        /** @var \Swis\JsonApi\Client\Collection|null $relatedCollection */
                        $relatedCollection = $relation->getData();

                        if ($relatedCollection === null) {
                            continue;
                        }

                        $relation->setIncluded(
                            $relatedCollection->map(fn (ItemInterface $relatedItem) => $this->getItem($keyedItems, $relatedItem) ?? $relatedItem)
                        );
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
     * @param \Swis\JsonApi\Client\Collection $items
     *
     * @return \Swis\JsonApi\Client\Collection
     */
    private function getDuplicateItems(Collection $items): Collection
    {
        return $items->duplicates(fn (ItemInterface $item) => $this->getItemKey($item));
    }
}
