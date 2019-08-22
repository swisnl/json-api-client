<?php

namespace Swis\JsonApi\Client\Tests\Providers;

use Swis\JsonApi\Client\Tests\AbstractTest;
use Swis\JsonApi\Client\Tests\Mocks\Items\ChildItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\MasterItem;
use Swis\JsonApi\Client\Tests\Mocks\MockTypeMapperServiceProvider;
use Swis\JsonApi\Client\TypeMapper;

class TypeMapperServiceProviderTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_registers_types_with_the_typemapper()
    {
        $provider = new MockTypeMapperServiceProvider($this->app);
        $typeMapper = $this->createMock(TypeMapper::class);
        $typeMapper->expects($this->exactly(2))
            ->method('setMapping')
            ->withConsecutive(
                ['master', MasterItem::class],
                ['child', ChildItem::class]
            );

        $provider->boot($typeMapper);
    }
}
