<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Core\Exception;

use RuntimeException as SplRuntimeException;

class RuntimeException extends SplRuntimeException implements ExceptionInterface
{
}
