<?php

namespace Swis\JsonApi\Interfaces;

interface ItemDocumentInterface extends DocumentInterface
{
    /**
     * @return ItemInterface
     */
    public function getData();

    /**
     * @return array
     */
    public function toArray();
}
