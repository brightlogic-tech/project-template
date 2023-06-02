<?php

declare(strict_types = 1);

namespace BrightLogic\Template\StorageFile;

use \CoolBeans\Attribute\Comment;
use \CoolBeans\PrimaryKey\IntPrimaryKey;

final class StorageFile extends \CoolBeans\Bean
{
    public IntPrimaryKey $id;
    public string $filename;
    public string $url;
    #[Comment('Files mime type')]
    public string $mime;
    #[Comment('File size in bytes')]
    public int $size;
    #[Comment('Photo width in pixels')]
    public ?int $photo_width = null;
    #[Comment('Photo height in pixels')]
    public ?int $photo_height = null;
    #[Comment('Whether photo is a 360 photo')]
    public ?bool $photo_is_360 = null;
    #[Comment('Video duration in miliseconds (1 / 1000 of second)')]
    public ?int $video_length = null;
    #[\CoolBeans\Attribute\DefaultValue(\CoolBeans\Attribute\Types\DefaultFunction::CURRENT_TIMESTAMP)]
    public \Nette\Utils\DateTime $created;
    public bool $valid = false;
}
