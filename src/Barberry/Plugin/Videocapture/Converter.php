<?php
namespace Barberry\Plugin\Videocapture;

use Barberry\Plugin;
use Barberry\ContentType;

class Converter implements Plugin\InterfaceConverter
{
    private $tempDir;

    public function __construct(ContentType $targetContentType, $tempDir)
    {
        $this->targetContentType = $targetContentType;
        $this->tempDir = $tempDir;
    }

    public function convert($bin, Plugin\InterfaceCommand $command = null)
    {
        $sourceFile = $this->createTempFile($bin);
        $destinationFile = $sourceFile . '.' . $this->targetContentType->standartExtention();

        if ($this->targetContentType->isImage()) {
            $cmd = $this->getCommandStringForVideoToImageConversion($sourceFile, $destinationFile, $command);
        }

        if ($this->targetContentType->isVideo()) {
            $cmd = $this->getCommandStringForVideoToVideoConversion($sourceFile, $destinationFile, $command);
        }

        exec('nice -n 0 ' . $cmd);
        if (filesize($destinationFile)) {
            $bin = file_get_contents($destinationFile);
        } else {
            unlink($sourceFile);
            unlink($destinationFile);
            throw new VideocaptureException('failed to convert to destination file');
        }

        if ($this->targetContentType->isImage() && !is_null($command->commandForImagemagick())) {
            $bin = $this->resizeWithImagemagick($bin, $command->commandForImagemagick());
        }

        unlink($sourceFile);
        unlink($destinationFile);
        return $bin;
    }

    private function createTempFile($content)
    {
        $tempFile = tempnam($this->tempDir, 'videocapture_');
        chmod($tempFile, 0664);
        file_put_contents($tempFile, $content, FILE_APPEND);
        return $tempFile;
    }

    private function getCommandStringForVideoToImageConversion($source, $destination, $command)
    {
        $from = escapeshellarg($source);
        $dimensions = $this->getOutputFileDimensions($command);
        $time = $this->getScreenshotTime($from, $command);
        $to = escapeshellarg($destination);
        return "ffmpeg {$time} -vframes 1 -i {$from} {$dimensions} -f image2 {$to} 2>&1";
    }

    private function getOutputFileDimensions($command)
    {
        if ($command->width() && $command->height()) {
            return '-s ' . escapeshellarg($command->width() . 'x' . $command->height());
        }
        return '';
    }

    private function getScreenshotTime($from, $command)
    {
        $seconds = 1;
        if ($command->screenshotTime()) {
            $seconds = $command->screenshotTime();
        }
        // get random time
        $out = shell_exec('ffmpeg -i ' . $from . ' 2>&1 | grep Duration');
        if (preg_match('/Duration: (\d+):(\d+):(\d+)/', $out, $time)) {
            $videoLength = ($time[1]*3600) + ($time[2]*60) + $time[3];
            $seconds = rand(1, $videoLength);
        }
        return '-ss ' . escapeshellarg($seconds);
    }

    private function getCommandStringForVideoToVideoConversion($source, $destination, $command)
    {
        $from = escapeshellarg($source);
        $dimensions = $this->getOutputFileDimensions($command);
        $audioBitrate = $this->getAudioBitrate($command);
        $videoBitrate = $this->getVideoBitrate($source, $command);
        $to = escapeshellarg($destination);
        return "ffmpeg -i {$from} {$dimensions} {$videoBitrate} {$audioBitrate} -sn -strict experimental {$to} 2>&1";
    }

    private function getVideoBitrate($source, $command)
    {
        $bitrate = '-vcodec copy';
        if ($command->videoBitrate()) {
            $bitrate = '-b ' . escapeshellarg($command->videoBitrate() . 'k');
        } else {
             $out = shell_exec('ffmpeg -i ' . $source . ' 2>&1 | grep bitrate:');
             if (preg_match('/^bitrate: (\d+)$/', $out, $matches)) {
                $bitrate = '-b ' . escapeshellarg($matches[1] . 'k');
            }
        }
        return $bitrate;
    }

    private function getAudioBitrate($command)
    {
        $bitrate = '-acodec copy';
        if ($command->audioBitrate()) {
            $bitrate = '-ab ' . escapeshellarg($command->audioBitrate() . 'k');
        }
        return $bitrate;
    }

    protected function resizeWithImagemagick($bin, $commandString)
    {
        $from = ucfirst(ContentType::byString($bin)->standartExtention());
        $to = ucfirst($this->targetContentType->standartExtention());
        $directionClass = '\\Barberry\\Direction\\' . $from . 'To' . $to . 'Direction';
        $direction = new $directionClass($commandString);
        return $direction->convert($bin);
    }
}
