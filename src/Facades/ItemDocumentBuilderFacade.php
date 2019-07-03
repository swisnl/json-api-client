<?php

namespace Swis\JsonApi\Client\Facades;

use Illuminate\Support\Facades\Facade;
use Swis\JsonApi\Client\ItemDocumentBuilder;

/**
 * @method static \Swis\JsonApi\Client\ItemDocument build(\Swis\JsonApi\Client\Interfaces\ItemInterface $item, array $attributes, string $id = null)
 *
 * @see \Swis\JsonApi\Client\ItemDocumentBuilder
 */
class ItemDocumentBuilderFacade extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return ItemDocumentBuilder::class;
    }
}
