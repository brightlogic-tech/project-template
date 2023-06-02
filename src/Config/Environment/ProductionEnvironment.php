<?php

declare(strict_types = 1);

namespace BrightLogic\Template\Config\Environment;

final class ProductionEnvironment implements Environment
{
    public function getFile() : string
    {
        return \BrightLogic\Template\Bootstrap::APP_ROOT . '/Config/production.neon';
    }

    public function getBaseTempDirectory() : string
    {
        return '/mnt/temp';
    }
}
