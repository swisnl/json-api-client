<?php

namespace Swis\JsonApi\Client\Facades;

use Illuminate\Support\Facades\Facade;
use Swis\JsonApi\Client\CollectionDocumentBuilder;

/**
 * @method static \Swis\JsonApi\Client\CollectionDocument build(array $items)
 *
 * @see \Swis\JsonApi\Client\CollectionDocumentBuilder
 */
class CollectionDocumentBuilderFacade extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return CollectionDocumentBuilder::class;
    }
}
