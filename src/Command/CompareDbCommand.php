<?php

declare(strict_types = 1);

namespace Infinityloop\Template\Command;

use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;

final class CompareDbCommand extends \Symfony\Component\Console\Command\Command
{
    protected static $defaultName = 'compareDb';

    public function __construct(
        private string $projectName,
        private \CoolBeans\Command\SqlGeneratorCommand $sqlGeneratorCommand,
    )
    {
        parent::__construct(self::$defaultName);
    }

    protected function configure() : void
    {
        $this->setName(self::$defaultName);
        $this->setDescription('Validates equality of current DB structure with remote DB version.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $currentStructure = $this->sqlGeneratorCommand->generate(\App\Bootstrap::APP_ROOT . '/Storage');
        $remoteStructure = $this->getRemoteStructure();

        if ($currentStructure === $remoteStructure) {
            $output->writeln('Databases are equal.');

            return self::SUCCESS;
        }

        $output->writeln('Databases are not equal. Abort automatic deployment.');

        return self::FAILURE;
    }

    private function getRemoteStructure() : string
    {
        $app = new \Symfony\Component\Console\Application();
        $app->add(new \Bref\Console\Command\Cli());

        $inputStr = new \Symfony\Component\Console\Input\StringInput(
            'cli --region eu-central-1 ' . $this->projectName . '-dev-console -- sqlGenerator ./../app/Storage',
        );
        $outputCapture = new class implements OutputInterface {
            /** @var array<string> */
            public array $messages = [];

            public function write($messages, bool $newline = false, int $options = 0) : void // @phpstan-ignore-line
            {
                $this->messages[] = $messages;
            }

            public function writeln($messages, int $options = 0) : void // @phpstan-ignore-line
            {
                $this->messages[] = $messages;
            }

            public function setVerbosity(int $level) : void
            {
            }

            public function getVerbosity() : int
            {
                return self::VERBOSITY_NORMAL;
            }

            public function isQuiet() : bool
            {
                return false;
            }

            public function isVerbose() : bool
            {
                return false;
            }

            public function isVeryVerbose() : bool
            {
                return false;
            }

            public function isDebug() : bool
            {
                return false;
            }

            public function setDecorated(bool $decorated) : void
            {
            }

            public function isDecorated() : bool
            {
                return false;
            }

            public function setFormatter(\Symfony\Component\Console\Formatter\OutputFormatterInterface $formatter) : void
            {
            }

            public function getFormatter() : \Symfony\Component\Console\Formatter\OutputFormatterInterface
            {
                return new \Symfony\Component\Console\Formatter\NullOutputFormatter();
            }
        };

        $app->doRun($inputStr, $outputCapture);
        $messages = $outputCapture->messages;

        if (\array_key_exists(0, $messages) && \count($messages) === 1) {
            return $messages[0];
        }

        throw new \RuntimeException(\implode('; ', $messages));
    }
}
