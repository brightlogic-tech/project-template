<?php

declare(strict_types = 1);

namespace Infinityloop\Template\Config\Execution;

final class DefaultExecution implements Execution
{
    public function getFile() : string
    {
        return \Infinityloop\Template\Bootstrap::APP_ROOT . 'Config/default.neon';
    }

    public function getSecondLevelTempDirectory() : string
    {
        return '/default';
    }
}
