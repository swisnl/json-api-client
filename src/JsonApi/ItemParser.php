<?php

namespace Swis\JsonApi\Client\JsonApi;

use Art4\JsonApiClient\RelationshipCollectionInterface;
use Art4\JsonApiClient\ResourceIdentifierCollectionInterface;
use Art4\JsonApiClient\ResourceIdentifierInterface;
use Art4\JsonApiClient\ResourceItemInterface;
use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Interfaces\DataInterface;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Interfaces\TypeMapperInterface;
use Swis\JsonApi\Client\Item;

/**
 * @internal
 */
class ItemParser
{
    /**
     * @var \Swis\JsonApi\Client\Interfaces\TypeMapperInterface
     */
    private $typeMapper;

    /**
     * @var \Swis\JsonApi\Client\JsonApi\LinksParser
     */
    private $linksParser;

    /**
     * @var \Swis\JsonApi\Client\JsonApi\MetaParser
     */
    private $metaParser;

    /**
     * @param \Swis\JsonApi\Client\Interfaces\TypeMapperInterface $typeMapper
     * @param \Swis\JsonApi\Client\JsonApi\LinksParser            $linksParser
     * @param \Swis\JsonApi\Client\JsonApi\MetaParser             $metaParser
     */
    public function __construct(TypeMapperInterface $typeMapper, LinksParser $linksParser, MetaParser $metaParser)
    {
        $this->typeMapper = $typeMapper;
        $this->linksParser = $linksParser;
        $this->metaParser = $metaParser;
    }

    /**
     * @param \Art4\JsonApiClient\ResourceItemInterface $jsonApiItem
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface
     */
    public function parse(ResourceItemInterface $jsonApiItem): ItemInterface
    {
        $item = $this->getItemInstance($jsonApiItem->get('type'));

        if ($jsonApiItem->has('id')) {
            $item->setId($jsonApiItem->get('id'));
        }

        if ($jsonApiItem->has('attributes')) {
            $item->fill($jsonApiItem->get('attributes')->asArray(true));
        }

        if ($jsonApiItem->has('relationships')) {
            $this->setRelations($item, $jsonApiItem->get('relationships'));
        }

        if ($jsonApiItem->has('links')) {
            $item->setLinks($this->linksParser->parse($jsonApiItem->get('links')->asArray()));
        }

        if ($jsonApiItem->has('meta')) {
            $item->setMeta($this->metaParser->parse($jsonApiItem->get('meta')));
        }

        return $item;
    }

    /**
     * @param string $type
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface
     */
    private function getItemInstance(string $type): ItemInterface
    {
        if ($this->typeMapper->hasMapping($type)) {
            return $this->typeMapper->getMapping($type);
        }

        return (new Item())->setType($type);
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface       $item
     * @param \Art4\JsonApiClient\RelationshipCollectionInterface $relationships
     */
    private function setRelations(ItemInterface $item, RelationshipCollectionInterface $relationships): void
    {
        /** @var \Art4\JsonApiClient\RelationshipInterface $relationship */
        foreach ($relationships->asArray() as $name => $relationship) {
            $data = new Collection();
            if ($relationship->has('data')) {
                $data = $this->parseRelationshipData($relationship->get('data'));
            }

            $links = null;
            if ($relationship->has('links')) {
                $links = $this->linksParser->parse($relationship->get('links')->asArray());
            }

            $meta = null;
            if ($relationship->has('meta')) {
                $meta = $this->metaParser->parse($relationship->get('meta'));
            }

            $item->setRelation($name, $data, $links, $meta);
        }
    }

    /**
     * @param \Art4\JsonApiClient\ResourceIdentifierInterface|\Art4\JsonApiClient\ResourceIdentifierCollectionInterface $data
     *
     * @throws \InvalidArgumentException
     *
     * @return \Swis\JsonApi\Client\Interfaces\DataInterface
     */
    private function parseRelationshipData($data): DataInterface
    {
        if ($data instanceof ResourceIdentifierInterface) {
            return $this->getItemInstance($data->get('type'))
                ->setId($data->get('id'));
        }

        if ($data instanceof ResourceIdentifierCollectionInterface) {
            return Collection::make($data->asArray())
                ->map(
                    function (ResourceIdentifierInterface $identifier) {
                        return $this->getItemInstance($identifier->get('type'))
                            ->setId($identifier->get('id'));
                    }
                );
        }

        throw new \InvalidArgumentException(sprintf('Expected either %s or %s', ResourceIdentifierInterface::class, ResourceIdentifierCollectionInterface::class));
    }
}
