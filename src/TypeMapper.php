<?php

namespace Swis\JsonApi\Client;

use Swis\JsonApi\Client\Exceptions\TypeMappingException;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Interfaces\TypeMapperInterface;

class TypeMapper implements TypeMapperInterface
{
    /**
     * @var array
     */
    protected $typeMappings = [];

    /**
     * @param string $type
     * @param string $class
     *
     * @throws \Swis\JsonApi\Client\Exceptions\TypeMappingException
     */
    public function setMapping(string $type, string $class): void
    {
        if (!class_exists($class)) {
            throw new TypeMappingException(sprintf('Class %s not found.', $class));
        }

        if (!is_subclass_of($class, ItemInterface::class)) {
            throw new TypeMappingException(sprintf('Class %s must implement %s.', $class, ItemInterface::class));
        }

        $this->typeMappings[$type] = $class;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function hasMapping(string $type): bool
    {
        return array_key_exists($type, $this->typeMappings);
    }

    /**
     * @param string $type
     */
    public function removeMapping(string $type): void
    {
        unset($this->typeMappings[$type]);
    }

    /**
     * @param string $type
     *
     *@throws \Swis\JsonApi\Client\Exceptions\TypeMappingException
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface
     */
    public function getMapping(string $type): ItemInterface
    {
        if (!array_key_exists($type, $this->typeMappings)) {
            throw new TypeMappingException(sprintf('No mapping for type %s', $type));
        }

        return new $this->typeMappings[$type]();
    }
}
