<?php

declare(strict_types = 1);

namespace BrightLogic\Template\Config\Execution;

interface Execution extends \BrightLogic\Template\Config\Config
{
    public function getSecondLevelTempDirectory() : string;
}
