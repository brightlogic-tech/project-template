<?php

declare(strict_types = 1);

namespace BrightLogic\Template\Config\Environment;

final class LocalEnvironment implements Environment
{
    public function getFile() : string
    {
        return \BrightLogic\Template\Bootstrap::APP_ROOT . '/Config/local.neon';
    }

    public function getBaseTempDirectory() : string
    {
        return \BrightLogic\Template\Bootstrap::PROJECT_ROOT . '/temp';
    }
}
