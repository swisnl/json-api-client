<?php

namespace Swis\JsonApi\Client\Interfaces;

/**
 * Class TypeMapperInterface.
 */
interface TypeMapperInterface
{
    /**
     * @param string $type
     * @param string $class
     */
    public function setMapping(string $type, string $class);

    /**
     * @param string $type
     *
     * @return bool
     */
    public function hasMapping(string $type);

    /**
     * @param string $type
     */
    public function removeMapping(string $type);

    /**
     * @param string $type
     *
     * @throws \InvalidArgumentException
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface
     */
    public function getMapping(string $type);
}
