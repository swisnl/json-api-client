<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

/**
 * Class TypeMapperInterface.
 */
interface TypeMapperInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function setMapping(string $type, string $class): void;

    public function hasMapping(string $type): bool;

    public function removeMapping(string $type): void;

    /**
     * @throws \InvalidArgumentException
     */
    public function getMapping(string $type): ItemInterface;
}
