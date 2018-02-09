<?php

namespace Swis\JsonApi\Client\Resource;

use Swis\JsonApi\Client\Interfaces\TypeMapperInterface;
use Swis\JsonApi\Client\Resource\Interfaces\ResourceInterface;

class ResourceRegistrar
{
    /**
     * @var \Swis\JsonApi\Client\Interfaces\TypeMapperInterface
     */
    private $typeMapper;

    /**
     * @param \Swis\JsonApi\Client\Interfaces\TypeMapperInterface $typeMapper
     */
    public function __construct(TypeMapperInterface $typeMapper)
    {
        $this->typeMapper = $typeMapper;
    }

    /**
     * @param \Swis\JsonApi\Client\Resource\Interfaces\ResourceInterface $resource
     */
    public function registerTypeMapping(ResourceInterface $resource)
    {
        $this->typeMapper->setMapping($resource->getItem()->getType(), get_class($resource->getItem()));
    }
}
