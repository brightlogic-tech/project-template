<?php

declare(strict_types = 1);

namespace BrightLogic\Template\StorageFile;

final class S3FileStorage implements FileStorage
{
    private \Aws\S3\S3Client $client;

    public function __construct(
        /** @var array<string, string|array<string, string>> */
        private array $s3Config,
        private StorageFileModel $storageFileModel,
    )
    {
        $this->client = new \Aws\S3\S3Client($this->s3Config);
    }

    public function getUploadLink() : string
    {
        return (string) $this->client->createPresignedRequest(
            $this->client->getCommand('PutObject', [
                'Bucket' => $this->storageFileModel->bucketName,
                'Key' => $this->storageFileModel->getRandomFileName(),
            ]),
            '+20 minutes',
        )->getUri();
    }

    public function getDownloadLink(string $fileName) : string
    {
        return (string) $this->client->createPresignedRequest(
            $this->client->getCommand('GetObject', [
                'Bucket' => $this->storageFileModel->bucketName,
                'Key' => $fileName,
            ]),
            '+20 minutes',
        )->getUri();
    }

    public function headObject(string $fileName) : FileHeadObjectResult
    {
        $result = $this->client->headObject([
            'Bucket' => $this->storageFileModel->bucketName,
            'Key' => $fileName,
        ]);

        return new FileHeadObjectResult($result['ContentType'], $result['ContentLength']);
    }

    public function getObject(string $fileName) : FileGetObjectResult
    {
        $result = $this->client->getObject([
            'Bucket' => $this->storageFileModel->bucketName,
            'Key' => $fileName,
        ]);

        return new FileGetObjectResult($result['ContentType'], $result['ContentLength'], $result['Body']);
    }

    public function putObject(string $fileName, string $pathToContent) : void
    {
        $this->client->putObject([
            'Bucket' => $this->storageFileModel->bucketName,
            'Key' => $fileName,
            'SourceFile' => $pathToContent,
        ]);
    }

    public function deleteObject(string $fileName) : void
    {
        $this->client->deleteObject([
            'Bucket' => $this->storageFileModel->bucketName,
            'Key' => $fileName,
        ]);
    }
}
