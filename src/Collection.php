<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client;

use Swis\JsonApi\Client\Interfaces\DataInterface;

/**
 * @template TKey of array-key
 * @template TValue of \Swis\JsonApi\Client\Interfaces\ItemInterface
 *
 * @extends \Illuminate\Support\Collection<TKey, TValue>
 */
class Collection extends \Illuminate\Support\Collection implements DataInterface
{
    /**
     * Get the collection of items as a plain json api array.
     */
    public function toJsonApiArray(): array
    {
        return array_map(
            static fn ($value) => $value instanceof DataInterface ? $value->toJsonApiArray() : $value,
            $this->items
        );
    }
}
