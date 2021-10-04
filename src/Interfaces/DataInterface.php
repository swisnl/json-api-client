<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

interface DataInterface
{
    /**
     * Get the data as a plain json api array.
     *
     * @return array
     */
    public function toJsonApiArray(): array;
}
