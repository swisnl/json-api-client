<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Exceptions;

use InvalidArgumentException;

class HydrationException extends InvalidArgumentException implements Exception
{
}
