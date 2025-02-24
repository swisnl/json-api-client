<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

interface TypedRelationInterface
{
    public function getType(): string;

    /**
     * @return static
     */
    public function setType(string $type);
}
