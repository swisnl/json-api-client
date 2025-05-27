<?php

use Swis\JsonApi\Client\DocumentClient;
use Swis\JsonApi\Client\DocumentFactory;
use Swis\JsonApi\Client\Tests\Mocks\Items\ChildItem;
use Swis\JsonApi\Client\Tests\Mocks\MockRepository;

use function PHPStan\Testing\assertType;

$repository = new MockRepository(DocumentClient::create(), new DocumentFactory);

assertType('Swis\JsonApi\Client\Interfaces\CollectionDocumentInterface<Swis\JsonApi\Client\Tests\Mocks\Items\ChildItem>', $repository->all());
assertType('Swis\JsonApi\Client\Interfaces\ItemDocumentInterface<Swis\JsonApi\Client\Tests\Mocks\Items\ChildItem>', $repository->find('foo'));
assertType('Swis\JsonApi\Client\Interfaces\ItemDocumentInterface<Swis\JsonApi\Client\Tests\Mocks\Items\ChildItem>', $repository->save(new ChildItem));
