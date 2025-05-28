<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Actions;

use Swis\JsonApi\Client\Interfaces\ItemInterface;

/**
 * @template TItem of \Swis\JsonApi\Client\Interfaces\ItemInterface
 */
trait Save
{
    /** @use Create<TItem> */
    use Create { create as protected saveNew; }

    /** @use Update<TItem> */
    use Update { update as protected saveExisting; }

    /**
     * @return \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface<TItem>
     */
    public function save(ItemInterface $item, array $parameters = [], array $headers = [])
    {
        if ($item->isNew()) {
            return $this->saveNew($item, $parameters, $headers);
        }

        return $this->saveExisting($item, $parameters, $headers);
    }
}
