<?php

namespace Swis\JsonApi\Client\Providers;

use Illuminate\Support\ServiceProvider;
use Swis\JsonApi\Client\Interfaces\TypeMapperInterface;

class TypeMapperServiceProvider extends ServiceProvider
{
    /**
     * A list of class names implementing \Swis\JsonApi\Client\Interfaces\ItemInterface.
     *
     * @var string[]
     */
    protected $items = [];

    /**
     * @param \Swis\JsonApi\Client\Interfaces\TypeMapperInterface $typeMapper
     */
    public function boot(TypeMapperInterface $typeMapper)
    {
        foreach ($this->items as $class) {
            /** @var \Swis\JsonApi\Client\Interfaces\ItemInterface $item */
            $item = $this->app->make($class);

            $typeMapper->setMapping($item->getType(), $class);
        }
    }
}
