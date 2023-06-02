<?php

declare(strict_types = 1);

namespace Infinityloop\Template\Config\Environment;

interface Environment extends \Infinityloop\Template\Config\Config
{
    public function getBaseTempDirectory() : string;
}
