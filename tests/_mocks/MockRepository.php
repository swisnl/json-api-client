<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests\Mocks;

use Swis\JsonApi\Client\Repository;

/**
 * @extends Repository<\Swis\JsonApi\Client\Tests\Mocks\ItemStub>
 */
class MockRepository extends Repository
{
    protected $endpoint = 'mocks';
}
