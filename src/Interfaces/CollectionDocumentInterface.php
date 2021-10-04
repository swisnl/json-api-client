<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

interface CollectionDocumentInterface extends DocumentInterface
{
    /**
     * @return \Swis\JsonApi\Client\Collection
     */
    public function getData();
}
