<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Exceptions;

use InvalidArgumentException;

class UnsupportedDataException extends InvalidArgumentException implements Exception
{
}
