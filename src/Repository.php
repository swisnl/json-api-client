<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client;

use Swis\JsonApi\Client\Actions\Delete;
use Swis\JsonApi\Client\Actions\FetchMany;
use Swis\JsonApi\Client\Actions\FetchOne;
use Swis\JsonApi\Client\Actions\Save;
use Swis\JsonApi\Client\Actions\TakeOne;
use Swis\JsonApi\Client\Interfaces\RepositoryInterface;

class Repository extends BaseRepository implements RepositoryInterface
{
    use Delete;
    use FetchMany;
    use FetchOne;
    use Save;
    use TakeOne;
}
