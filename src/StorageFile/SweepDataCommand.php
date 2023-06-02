<?php

declare(strict_types = 1);

namespace Infinityloop\Template\StorageFile;

use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;

final class SweepDataCommand extends \Symfony\Component\Console\Command\Command
{
    protected static $defaultName = 'sweepData';

    public function __construct(
        private StorageFileModel $storageFileModel,
        private FileStorage $fileStorage,
        private \Infinityloop\Template\Logging\LogTable $logTable,
    )
    {
        parent::__construct(self::$defaultName);
    }

    public function operation() : int
    {
        $files = $this->storageFileModel->findByArray([
            'created <= ?' => new \Nette\Utils\DateTime('-2 hours'),
            'valid' => 0,
        ]);

        foreach ($files as $storageFile) {
            $this->fileStorage->deleteObject($storageFile->filename);
            $this->storageFileModel->delete($storageFile->id);
        }

        $this->logTable->deleteByArray([
            'level' => ['debug', 'info', 'queue'],
            'time <= ?' => new \Nette\Utils\DateTime('-7 days'),
        ]);

        return self::SUCCESS;
    }

    protected function configure() : void
    {
        $this->setName(self::$defaultName);
        $this->setDescription('Remove unused logs and invalid files from S3.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        return $this->operation();
    }
}
