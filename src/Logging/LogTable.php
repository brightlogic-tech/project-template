<?php

declare(strict_types = 1);

namespace BrightLogic\Template\Logging;

final class LogTable extends \CoolBeans\Table
{
    public function __construct(\Nette\Database\Explorer $context)
    {
        parent::__construct('log', $context);
    }
}
