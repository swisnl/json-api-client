<?php

namespace Swis\JsonApi\Client\Facades;

use Illuminate\Support\Facades\Facade;
use Swis\JsonApi\Client\ItemHydrator;

/**
 * @method static \Swis\JsonApi\Client\Interfaces\ItemInterface hydrate(\Swis\JsonApi\Client\Interfaces\ItemInterface $item, array $attributes)
 *
 * @see \Swis\JsonApi\Client\ItemHydrator
 */
class ItemHydratorFacade extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return ItemHydrator::class;
    }
}
