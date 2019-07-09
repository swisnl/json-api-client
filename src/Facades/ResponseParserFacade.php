<?php

namespace Swis\JsonApi\Client\Facades;

use Illuminate\Support\Facades\Facade;
use Swis\JsonApi\Client\Interfaces\ResponseParserInterface;

/**
 * @method static \Swis\JsonApi\Client\Interfaces\DocumentInterface parse(\Psr\Http\Message\ResponseInterface $response)
 *
 * @see \Swis\JsonApi\Client\Interfaces\ResponseParserInterface
 */
class ResponseParserFacade extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return ResponseParserInterface::class;
    }
}
