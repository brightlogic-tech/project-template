<?php

declare(strict_types = 1);

namespace Infinityloop\Template\Command;

use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;

final class ClearCacheCommand extends \Symfony\Component\Console\Command\Command
{
    protected static $defaultName = 'clearCache';

    public function __construct(
        protected \Nette\Caching\Storage $storage,
    )
    {
        parent::__construct(self::$defaultName);
    }

    protected function configure() : void
    {
        $this->setName(self::$defaultName);
        $this->setDescription('Clears nette/cache storage and filesystem cache.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $output->write('Clear redis cache: ');
        $this->storage->clean([\Nette\Caching\Cache::ALL => true]);
        $output->writeln('done');

        $output->write('Clear filesystem cache: ');
        \system('rm -rf ' . \App\Bootstrap::$environment->getBaseTempDirectory() . '/*');
        $output->writeln('done');

        return 0;
    }
}
