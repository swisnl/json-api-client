<?php

namespace Swis\JsonApi\Client\Facades;

use Illuminate\Support\Facades\Facade;
use Swis\JsonApi\Client\Interfaces\DocumentParserInterface;

/**
 * @method static \Swis\JsonApi\Client\Interfaces\DocumentInterface parse(string $json)
 *
 * @see \Swis\JsonApi\Client\Interfaces\DocumentParserInterface
 */
class DocumentParserFacade extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return DocumentParserInterface::class;
    }
}
