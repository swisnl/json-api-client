<?php

namespace Swis\JsonApi\Client\Facades;

use Illuminate\Support\Facades\Facade;
use Swis\JsonApi\Client\DocumentFactory;

/**
 * @method static \Swis\JsonApi\Client\ItemDocument|\Swis\JsonApi\Client\CollectionDocument make(\Swis\JsonApi\Client\Interfaces\DataInterface $data)
 *
 * @see \Swis\JsonApi\Client\DocumentFactory
 */
class DocumentFactoryFacade extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return DocumentFactory::class;
    }
}
