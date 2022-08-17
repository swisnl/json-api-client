<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Actions;

use Swis\JsonApi\Client\Interfaces\ItemInterface;

trait Save
{
    use Create { create as protected saveNew; }
    use Update { update as protected saveExisting; }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     * @param array                                         $parameters
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function save(ItemInterface $item, array $parameters = [])
    {
        if ($item->isNew()) {
            return $this->saveNew($item, $parameters);
        }

        return $this->saveExisting($item, $parameters);
    }
}
