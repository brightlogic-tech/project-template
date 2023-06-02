<?php

declare(strict_types = 1);

namespace BrightLogic\Template\StorageFile;

final class FileNotFound extends \Graphpinator\Exception\GraphpinatorBase
{
    public const MESSAGE = 'File not found - specified file was not found in storage.';

    public function isOutputable() : bool
    {
        return true;
    }
}
