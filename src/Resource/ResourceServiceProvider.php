<?php

namespace Swis\JsonApi\Client\Resource;

use Illuminate\Support\ServiceProvider;

class ResourceServiceProvider extends ServiceProvider
{
    /**
     * @var \Swis\JsonApi\Client\Resource\Interfaces\ResourceInterface[]
     */
    protected $resources = [];

    /**
     * @param \Swis\JsonApi\Client\Resource\ResourceRegistrar $resourceRegistrar
     */
    public function boot(ResourceRegistrar $resourceRegistrar)
    {
        foreach ($this->resources as $resource) {
            $resourceInstance = $this->app->make($resource);

            $resourceRegistrar->registerTypeMapping($resourceInstance);
        }
    }
}
