<?php

namespace Swis\JsonApi\Client\Facades;

use Illuminate\Support\Facades\Facade;
use Swis\JsonApi\Client\Interfaces\TypeMapperInterface;

/**
 * @method static void setMapping(string $type, string $class)
 * @method static bool hasMapping(string $type)
 * @method static void removeMapping(string $type)
 * @method static \Swis\JsonApi\Client\Interfaces\ItemInterface getMapping(string $type)
 *
 * @see \Swis\JsonApi\Client\Interfaces\TypeMapperInterface
 */
class TypeMapperFacade extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return TypeMapperInterface::class;
    }
}
