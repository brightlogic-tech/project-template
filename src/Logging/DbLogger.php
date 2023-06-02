<?php

declare(strict_types = 1);

namespace BrightLogic\Template\Logging;

final class DbLogger implements \Tracy\ILogger
{
    public function __construct(
        private \BrightLogic\Template\Logging\LogTable $logTable,
    )
    {
    }

    public function log(mixed $value, mixed $level = self::INFO) : void
    {
        $message = \Tracy\Logger::formatMessage($value);

        try {
            $this->logTable->insert([
                'level' => $level,
                'head' => \substr($message, 0, 200),
                'message' => $message,
            ]);
        } catch (\Throwable $e) {
            $stream = \fopen('php://stderr', 'wb');
            \fwrite($stream, 'Unable to write following log to database:' . \PHP_EOL);
            \fwrite($stream, $message . \PHP_EOL);
            \fwrite($stream, 'Exception in database write:' . \PHP_EOL);
            \fwrite($stream, $e->getMessage() . \PHP_EOL);
            \fclose($stream);
        }
    }
}
