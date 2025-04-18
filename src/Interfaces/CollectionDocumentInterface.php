<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

/**
 * @template TItem of \Swis\JsonApi\Client\Interfaces\ItemInterface
 */
interface CollectionDocumentInterface extends DocumentInterface
{
    /**
     * @return \Swis\JsonApi\Client\Collection<array-key, TItem>
     */
    public function getData();
}
