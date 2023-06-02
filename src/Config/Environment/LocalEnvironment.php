<?php

declare(strict_types = 1);

namespace Infinityloop\Template\Config\Environment;

final class LocalEnvironment implements Environment
{
    public function getFile() : string
    {
        return \Infinityloop\Template\Bootstrap::APP_ROOT . 'Config/local.neon';
    }

    public function getBaseTempDirectory() : string
    {
        return \Infinityloop\Template\Bootstrap::PROJECT_ROOT . '/temp';
    }
}
