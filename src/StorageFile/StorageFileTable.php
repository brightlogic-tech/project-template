<?php

declare(strict_types = 1);

namespace Infinityloop\Template\StorageFile;

final class StorageFileTable extends \CoolBeans\Table
{
    private const TABLE_NAME = 'storage_file';

    public function __construct(\Nette\Database\Explorer $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }
}
