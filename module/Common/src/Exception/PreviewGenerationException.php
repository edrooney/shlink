<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Common\Exception;

use function sprintf;

class PreviewGenerationException extends RuntimeException
{
    public static function fromImageError(string $error): self
    {
        return new self(sprintf('Error generating a preview image with error: %s', $error));
    }
}
