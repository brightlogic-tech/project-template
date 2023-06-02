<?php

declare(strict_types = 1);

namespace Infinityloop\Template\StorageFile;

final class FileGetObjectResult
{
    public function __construct(
        public readonly string $contentType,
        public readonly int $contentLength,
        public \Psr\Http\Message\StreamInterface $body,
    )
    {
    }
}
