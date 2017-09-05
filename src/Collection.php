<?php

namespace Swis\JsonApi;

use Swis\JsonApi\Interfaces\DataInterface;

class Collection extends \Illuminate\Support\Collection implements DataInterface
{
    /**
     * Get the collection of items as a plain json api array.
     *
     * @return array
     */
    public function toJsonApiArray(): array
    {
        return array_map(
            function ($value) {
                return $value instanceof DataInterface ? $value->toJsonApiArray() : $value;
            },
            $this->items
        );
    }
}
