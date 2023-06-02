<?php

declare(strict_types = 1);

namespace Infinityloop\Template\Config\Environment;

final class ProductionEnvironment implements Environment
{
    public function getFile() : string
    {
        return \Infinityloop\Template\Bootstrap::APP_ROOT . 'Config/production.neon';
    }

    public function getBaseTempDirectory() : string
    {
        return '/mnt/temp';
    }
}
