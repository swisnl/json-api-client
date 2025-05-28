<?php

use Swis\JsonApi\Client\Tests\Mocks\Items\ParentItem;

use function PHPStan\Testing\assertType;

$item = new ParentItem;

assertType('Swis\JsonApi\Client\Tests\Mocks\Items\ChildItem|null', $item->child()->getData());
assertType('Swis\JsonApi\Client\Collection<int, Swis\JsonApi\Client\Tests\Mocks\Items\ChildItem>|null', $item->children()->getData());

assertType('Swis\JsonApi\Client\Interfaces\ItemInterface|null', $item->morph()->getData());
assertType('Swis\JsonApi\Client\Collection<int, Swis\JsonApi\Client\Interfaces\ItemInterface>|null', $item->morphmany()->getData());
