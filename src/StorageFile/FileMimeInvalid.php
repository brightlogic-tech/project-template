<?php

declare(strict_types = 1);

namespace Infinityloop\Template\StorageFile;

final class FileMimeInvalid extends \Graphpinator\Exception\GraphpinatorBase
{
    public const MESSAGE = 'File with incorrect mimetype - specified file cannot be used in this category.';

    public function isOutputable() : bool
    {
        return true;
    }
}
