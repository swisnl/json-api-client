<?php

use Swis\JsonApi\Client\DocumentClient;
use Swis\JsonApi\Client\DocumentFactory;
use Swis\JsonApi\Client\Tests\Mocks\ItemStub;
use Swis\JsonApi\Client\Tests\Mocks\MockRepository;

use function PHPStan\Testing\assertType;

$repository = new MockRepository(DocumentClient::create(), new DocumentFactory);

$all = $repository->all();
assertType('Swis\JsonApi\Client\Interfaces\CollectionDocumentInterface<Swis\JsonApi\Client\Tests\Mocks\ItemStub>', $all);
assertType('Swis\JsonApi\Client\Collection<int, Swis\JsonApi\Client\Tests\Mocks\ItemStub>', $all->getData());
assertType('Swis\JsonApi\Client\Tests\Mocks\ItemStub|null', $all->getData()->first());

$find = $repository->find('foo');
assertType('Swis\JsonApi\Client\Interfaces\ItemDocumentInterface<Swis\JsonApi\Client\Tests\Mocks\ItemStub>', $find);
assertType('Swis\JsonApi\Client\Tests\Mocks\ItemStub', $find->getData());

$take = $repository->take();
assertType('Swis\JsonApi\Client\Interfaces\ItemDocumentInterface<Swis\JsonApi\Client\Tests\Mocks\ItemStub>', $take);
assertType('Swis\JsonApi\Client\Tests\Mocks\ItemStub', $take->getData());

$save = $repository->save(new ItemStub);
assertType('Swis\JsonApi\Client\Interfaces\ItemDocumentInterface<Swis\JsonApi\Client\Tests\Mocks\ItemStub>', $save);
assertType('Swis\JsonApi\Client\Tests\Mocks\ItemStub', $save->getData());
