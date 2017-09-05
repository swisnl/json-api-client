<?php

namespace Swis\JsonApi\Resource;

use Swis\JsonApi\Interfaces\TypeMapperInterface;
use Swis\JsonApi\Resource\Interfaces\ResourceInterface;

class ResourceRegistrar
{
    /**
     * @var \Swis\JsonApi\Interfaces\TypeMapperInterface
     */
    private $typeMapper;

    /**
     * @param \Swis\JsonApi\Interfaces\TypeMapperInterface $typeMapper
     */
    public function __construct(TypeMapperInterface $typeMapper)
    {
        $this->typeMapper = $typeMapper;
    }

    /**
     * @param \Swis\JsonApi\Resource\Interfaces\ResourceInterface $resource
     */
    public function registerTypeMapping(ResourceInterface $resource)
    {
        $this->typeMapper->setMapping($resource->getItem()->getType(), get_class($resource->getItem()));
    }
}
