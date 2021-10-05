<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

interface TypedRelationInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $type
     *
     * @return static
     */
    public function setType(string $type);
}
