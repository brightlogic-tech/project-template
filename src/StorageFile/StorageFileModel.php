<?php

declare(strict_types = 1);

namespace BrightLogic\Template\StorageFile;

/**
 * @method StorageFile getRow(\CoolBeans\Contract\PrimaryKey $key) : \CoolBeans\Bean
 * @method StorageFileSelection findAll() : \CoolBeans\Selection
 * @method StorageFileSelection findByArray(array $filter) : \CoolBeans\Selection
 */
final class StorageFileModel implements \CoolBeans\DataSource
{
    use \CoolBeans\TDecorator;

    public function __construct(
        public readonly string $bucketName,
        public readonly string $bucketUrl,
        private StorageFileTable $storageFileTable,
    )
    {
        $this->dataSource = new \CoolBeans\Decorator\Bean($storageFileTable, StorageFile::class, StorageFileSelection::class);
    }

    public function getRandomFileName() : string
    {
        do {
            $fileName = \Ramsey\Uuid\Uuid::uuid4()->toString();
        } while ($this->findAll()->where('filename', $fileName)->count() > 0);

        return $fileName;
    }

    /**
     * @param array<string> $allowedMimes
     */
    public function getAndValidateFile(string $fileUrl, array $allowedMimes, int $secondsToKeepTrying = 0) : StorageFile
    {
        $fileUrl = self::canonicalizeUrl($fileUrl);

        if (!\str_starts_with($fileUrl, $this->bucketUrl)) {
            throw new FileUrlInvalid();
        }

        $result = $this->getFileWithRetry($fileUrl, $secondsToKeepTrying);

        if (!\in_array($result->mime, $allowedMimes, true)) {
            throw new FileMimeInvalid();
        }

        $this->update($result->id, ['valid' => 1]);

        return $result;
    }

    private static function canonicalizeUrl(string $url) : string
    {
        if (\str_contains($url, '?')) {
            return \substr($url, 0, \strpos($url, '?'));
        }

        if (\str_contains($url, '#')) {
            return \substr($url, 0, \strpos($url, '#'));
        }

        return $url;
    }

    private function getFileWithRetry(string $url, int $maxWaiting) : StorageFile
    {
        $maxWaiting *= 1_000_000; // convert to microseconds
        $waiting = 100_000;

        do {
            $result = $this->findAll()->where('url', $url)->fetch();

            if ($result instanceof StorageFile) {
                return $result;
            }

            if ($waiting > $maxWaiting) {
                throw new FileNotFound();
            }

            \usleep($waiting);

            $waiting *= 2; // double the waiting time for next iteration
        } while (true);
    }
}
