<?php

declare(strict_types = 1);

namespace BrightLogic\Template\Config\Execution;

final class DefaultExecution implements Execution
{
    public function getFile() : string
    {
        return \BrightLogic\Template\Bootstrap::APP_ROOT . 'Config/default.neon';
    }

    public function getSecondLevelTempDirectory() : string
    {
        return '/default';
    }
}
