<?php

declare(strict_types = 1);

namespace BrightLogic\Template\StorageFile;

final class FileUrlInvalid extends \Graphpinator\Exception\GraphpinatorBase
{
    public const MESSAGE = 'File url invalid - url must origin from our storage and must use https.';

    public function isOutputable() : bool
    {
        return true;
    }
}
