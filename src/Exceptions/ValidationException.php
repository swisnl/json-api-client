<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Exceptions;

use InvalidArgumentException;

class ValidationException extends InvalidArgumentException implements Exception
{
}
