<?php

namespace Swis\JsonApi\Client\Interfaces;

interface ItemDocumentInterface extends DocumentInterface
{
    /**
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface
     */
    public function getData();

    /**
     * @return array
     */
    public function toArray();
}
