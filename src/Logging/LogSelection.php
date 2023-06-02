<?php

declare(strict_types = 1);

namespace Infinityloop\Template\Logging;

/**
 * @method Log current() : \CoolBeans\Bean
 * @method Log|null fetch() : ?\CoolBeans\Bean
 */
final class LogSelection extends \CoolBeans\Selection
{
    protected const ROW_CLASS = Log::class;
}
