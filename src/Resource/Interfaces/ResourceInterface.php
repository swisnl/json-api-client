<?php

namespace Swis\JsonApi\Resource\Interfaces;

use Swis\JsonApi\Interfaces\ItemInterface;

interface ResourceInterface
{
    /**
     * @return \Swis\JsonApi\Interfaces\ItemInterface
     */
    public function getItem(): ItemInterface;
}
