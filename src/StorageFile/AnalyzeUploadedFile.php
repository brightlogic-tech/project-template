<?php

declare(strict_types = 1);

namespace BrightLogic\Template\StorageFile;

final class AnalyzeUploadedFile extends \Bref\Event\S3\S3Handler implements \BrightLogic\Template\Operation
{
    public const IMAGE_TYPES = [
        'image/jpeg',
        'image/png',
        'image/svg+xml',
    ];
    public const VIDEO_TYPES = [
        'video/mp4',
        'video/avi',
        'video/x-ms-wmv',
        'video/quicktime',
    ];

    public function __construct(
        private StorageFileModel $storageFileModel,
        private FileStorage $fileStorage,
    )
    {
    }

    public function operation(string $file) : void
    {
        $headResult = $this->fileStorage->headObject($file);
        $insert = [
            'filename' => $file,
            'url' => $this->storageFileModel->bucketUrl . $file,
            'mime' => $headResult->contentType,
            'size' => $headResult->contentLength,
            'valid' => 0,
        ];

        if (\in_array($insert['mime'], self::IMAGE_TYPES, true)) {
            $getResult = $this->fileStorage->getObject($file);
            $imagick = new \Imagick();
            $imagick->readImageBlob($getResult->body->getContents());

            $insert['photo_width'] = $imagick->getImageWidth();
            $insert['photo_height'] = $imagick->getImageHeight();
            $insert['photo_is_360'] = (int) $this->detect360($imagick);
        } elseif (\in_array($insert['mime'], self::VIDEO_TYPES, true)) {
            $url = $this->fileStorage->getDownloadLink($file);
            $mediaInfo = new \Mhor\MediaInfo\MediaInfo();
            $mediaInfo->setConfig('command', \BrightLogic\Template\Bootstrap::PROJECT_ROOT . '/runtime/mediainfo/mediainfo');
            $duration = $mediaInfo->getInfo($url)->getGeneral()?->get('duration');
            \assert($duration instanceof \Mhor\MediaInfo\Attribute\Duration);

            $insert['video_length'] = $duration->getMilliseconds();
        }

        $this->storageFileModel->insert($insert);
    }

    public function handleS3(\Bref\Event\S3\S3Event $event, \Bref\Context\Context $context) : void
    {
        $this->operation($event->getRecords()[0]->getObject()->getKey());
    }

    private function detect360(\Imagick $image) : bool
    {
        if ($image->getImageMimeType() !== 'image/jpeg') {
            return false;
        }

        $metadata = $image->getImageProperties();

        if (\array_key_exists('Projection Type', $metadata) && $metadata['Projection Type'] === 'equirectangular') {
            return true;
        }

        if (\array_key_exists('Use Panorama Viewer', $metadata) && $metadata['Use Panorama Viewer'] === 'True') {
            return true;
        }

        return $image->getImageWidth() >= 5000 && ($image->getImageWidth() === ($image->getImageHeight() * 2));
    }
}
