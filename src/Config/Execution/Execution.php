<?php

declare(strict_types = 1);

namespace Infinityloop\Template\Config\Execution;

interface Execution extends \Infinityloop\Template\Config\Config
{
    public function getSecondLevelTempDirectory() : string;
}
