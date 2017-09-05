<?php

namespace Swis\JsonApi\Interfaces;

interface CollectionDocumentInterface extends DocumentInterface
{
    /**
     * @return \Swis\JsonApi\Collection
     */
    public function getData();
}
