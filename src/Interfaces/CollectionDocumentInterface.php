<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

use Swis\JsonApi\Client\Collection;

/**
 * @template TItem of \Swis\JsonApi\Client\Interfaces\ItemInterface
 */
interface CollectionDocumentInterface extends DocumentInterface
{
    /**
     * @return Collection<int, TItem>
     */
    public function getData();
}
