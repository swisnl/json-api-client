<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

/**
 * @template TItem of \Swis\JsonApi\Client\Interfaces\ItemInterface
 */
interface ItemDocumentInterface extends DocumentInterface
{
    /**
     * @return TItem
     */
    public function getData();
}
