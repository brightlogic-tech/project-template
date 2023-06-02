<?php

declare(strict_types = 1);

namespace Infinityloop\Template\Logging;

/**
 * @method Log getRow(\CoolBeans\Contract\PrimaryKey $key) : \CoolBeans\Bean
 * @method LogSelection findAll() : \CoolBeans\Selection
 * @method LogSelection findByArray(array $filter) : \CoolBeans\Selection
 */
final class LogModel implements \CoolBeans\DataSource
{
    use \CoolBeans\TDecorator;

    public function __construct(
        private LogTable $baseTable,
    )
    {
        $this->dataSource = new \CoolBeans\Decorator\Bean($baseTable, Log::class, LogSelection::class);
    }
}
