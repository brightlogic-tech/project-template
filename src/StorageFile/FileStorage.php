<?php

declare(strict_types = 1);

namespace BrightLogic\Template\StorageFile;

interface FileStorage
{
    public function getUploadLink() : string;

    public function getDownloadLink(string $fileName) : string;

    public function headObject(string $fileName) : FileHeadObjectResult;

    public function getObject(string $fileName) : FileGetObjectResult;

    public function putObject(string $fileName, string $pathToContent) : void;

    public function deleteObject(string $fileName) : void;
}
