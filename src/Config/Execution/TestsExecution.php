<?php

declare(strict_types = 1);

namespace BrightLogic\Template\Config\Execution;

final class TestsExecution implements Execution
{
    public function getFile() : string
    {
        return \BrightLogic\Template\Bootstrap::APP_ROOT . '/Config/tests.neon';
    }

    public function getSecondLevelTempDirectory() : string
    {
        return '/tests';
    }
}
