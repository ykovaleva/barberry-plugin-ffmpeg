<?php
namespace Barberry\Plugin\Ffmpeg;

use Barberry\Plugin;
use Barberry\ContentType;
use Barberry\Exception;

class Converter implements Plugin\InterfaceConverter
{
    const DESTINATION_IS_VIDEO = 'video';
    const DESTINATION_IS_IMAGE = 'image';

    private $tempDir;
    private $destinationType;
    private $targetContentType;
    private $processor;

    public function __construct($destinationType)
    {
        $this->destinationType = $destinationType;
    }

    public function configure(ContentType $targetContentType, $tempDir)
    {
        $this->targetContentType = $targetContentType;
        $this->tempDir = $tempDir;
        $this->processor = $this->selectProcessor();

        return $this;
    }

    public function convert($bin, Plugin\InterfaceCommand $command = null)
    {
        $sourceFile = $this->createTempFile($bin);
        $destinationFile = $sourceFile . '.' . $this->targetContentType->standardExtension();

        $this->processor->init($sourceFile, $destinationFile, $command);
        $this->processor->process();
        unlink($sourceFile);

        if (is_file($destinationFile) && filesize($destinationFile)) {
            $bin = file_get_contents($destinationFile);
            unlink($destinationFile);
        } else {
            if (is_file($destinationFile)) {
                unlink($destinationFile);
            }
            throw new Exception\ConversionNotPossible('failed to convert to destination file');
        }

        return $bin;
    }

    private function createTempFile($content)
    {
        $tempFile = tempnam($this->tempDir, 'ffmpeg_');
        chmod($tempFile, 0664);
        file_put_contents($tempFile, $content, FILE_APPEND);
        return $tempFile;
    }

    private function selectProcessor()
    {
        if ($this->destinationType === self::DESTINATION_IS_IMAGE) {
            return new VideoToImageProcessor($this->targetContentType);
        } else {
            return new VideoToVideoProcessor($this->targetContentType);
        }
    }
}
