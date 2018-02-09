<?php

namespace Swis\JsonApi\Client\Resource\Interfaces;

use Swis\JsonApi\Client\Interfaces\ItemInterface;

interface ResourceInterface
{
    /**
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface
     */
    public function getItem(): ItemInterface;
}
