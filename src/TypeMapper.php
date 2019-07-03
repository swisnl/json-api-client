<?php

namespace Swis\JsonApi\Client;

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
     * @throws \InvalidArgumentException
     */
    public function setMapping(string $type, string $class): void
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class %s not found.', $class));
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
     * @throws \InvalidArgumentException
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface
     */
    public function getMapping(string $type): ItemInterface
    {
        if (!array_key_exists($type, $this->typeMappings)) {
            throw new \InvalidArgumentException(sprintf('No mapping for type %s', $type));
        }

        return new $this->typeMappings[$type]();
    }
}
