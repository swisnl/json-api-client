<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

interface ItemDocumentInterface extends DocumentInterface
{
    /**
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface
     */
    public function getData();
}
