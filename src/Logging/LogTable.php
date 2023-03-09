<?php

declare(strict_types = 1);

namespace Infinityloop\Template\Logging;

final class LogTable extends \CoolBeans\Table
{
    private const TABLE_NAME = 'log';
  
    public function __construct(\Nette\Database\Explorer $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }
}
