<?php

declare(strict_types = 1);

namespace Infinityloop\Template\Config\Execution;

final class TestsExecution implements Execution
{
    public function getFile() : string
    {
        return \Infinityloop\Template\Bootstrap::APP_ROOT . 'Config/tests.neon';
    }

    public function getSecondLevelTempDirectory() : string
    {
        return '/tests';
    }
}
