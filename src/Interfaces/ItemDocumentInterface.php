<?php

namespace Swis\JsonApi\Interfaces;

interface ItemDocumentInterface extends DocumentInterface
{
    /**
     * @return \Swis\JsonApi\Interfaces\ItemInterface
     */
    public function getData();

    /**
     * @return array
     */
    public function toArray();
}
