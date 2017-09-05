<?php

namespace Swis\JsonApi;

use Swis\JsonApi\Interfaces\TypeMapperInterface;

class TypeMapper implements TypeMapperInterface
{
    /**
     * @var array
     */
    protected $typeMappings = [];

    /**
     * @param string $type
     * @param string $class
     */
    public function setMapping(string $type, string $class)
    {
        $this->typeMappings[$type] = $class;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function hasMapping(string $type)
    {
        return array_key_exists($type, $this->typeMappings);
    }

    /**
     * @param string $type
     */
    public function removeMapping(string $type)
    {
        unset($this->typeMappings[$type]);
    }

    /**
     * @param string $type
     *
     * @throws \InvalidArgumentException
     *
     * @return \Swis\JsonApi\Interfaces\ItemInterface
     */
    public function getMapping(string $type)
    {
        if (!array_key_exists($type, $this->typeMappings)) {
            throw new \InvalidArgumentException("No mapping for type $type");
        }

        $class = $this->typeMappings[$type];

        if (!class_exists($class)) {
            throw new \InvalidArgumentException("Class $class not found.");
        }

        return new $class();
    }
}
