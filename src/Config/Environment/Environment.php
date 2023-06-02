<?php

declare(strict_types = 1);

namespace BrightLogic\Template\Config\Environment;

interface Environment extends \BrightLogic\Template\Config\Config
{
    public function getBaseTempDirectory() : string;
}
