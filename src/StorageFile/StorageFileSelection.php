<?php

declare(strict_types = 1);

namespace BrightLogic\Template\StorageFile;

/**
 * @method StorageFile current() : \CoolBeans\Bean
 * @method StorageFile|null fetch() : ?\CoolBeans\Bean
 */
final class StorageFileSelection extends \CoolBeans\Selection
{
    protected const ROW_CLASS = StorageFile::class;
}
