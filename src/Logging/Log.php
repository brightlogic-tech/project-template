<?php

declare(strict_types = 1);

namespace BrightLogic\Template\Logging;

final class Log extends \CoolBeans\Bean
{
    public \CoolBeans\PrimaryKey\IntPrimaryKey $id;
    #[\CoolBeans\Attribute\DefaultValue(\CoolBeans\Attribute\Types\DefaultFunction::CURRENT_TIMESTAMP)]
    public \Nette\Utils\DateTime $time;
    public string $level;
    public string $head;
    #[\CoolBeans\Attribute\TypeOverride(\CoolBeans\Attribute\Types\ColumnType::TEXT)]
    public string $message;
}
