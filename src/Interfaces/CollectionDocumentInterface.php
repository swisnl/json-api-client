<?php

namespace Swis\JsonApi\Interfaces;

use Illuminate\Support\Collection;

interface CollectionDocumentInterface extends DocumentInterface
{
    /**
     * @return Collection
     */
    public function getData();
}
