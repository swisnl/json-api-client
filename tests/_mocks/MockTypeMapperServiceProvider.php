<?php

namespace Swis\JsonApi\Client\Tests\Mocks;

use Swis\JsonApi\Client\Providers\TypeMapperServiceProvider;
use Swis\JsonApi\Client\Tests\Mocks\Items\ChildItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\MasterItem;

class MockTypeMapperServiceProvider extends TypeMapperServiceProvider
{
    protected $items = [
        MasterItem::class,
        ChildItem::class,
    ];
}
