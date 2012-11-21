<?php
namespace Barberry\Plugin\Ffmpeg;

use Barberry\Exception;

class VideoToVideoProcessor implements VideoProcessorInterface
{
    private $source;
    private $destination;
    private $command;
    private $targetContentType;

    public function __construct(\Barberry\ContentType $targetContentType)
    {
        $this->targetContentType = $targetContentType;
    }

    public function init($source, $destination, $command)
    {
        $this->source = $source;
        $this->destination = $destination;
        $this->command = $command;
    }

    public function process()
    {
        $ffmpeg = new FfmpegDefaultParams($this->source, $this->targetContentType);

        $from = escapeshellarg($this->source);
        $outputDimension = $this->outputDimension() ? '-s ' . $this->outputDimension() : null;
        $audioCodec = '-acodec ' . ($this->command->audioCodec() ?: $ffmpeg->audioCodec());
        $videoCodec = '-vcodec ' . ($this->command->videoCodec() ?: $ffmpeg->videoCodec());
        $rotationIndex = ($this->command->rotation() ? '-vf transpose=' : ($ffmpeg->rotationIndex() ? '-vf transpose=' . $ffmpeg->rotationIndex() : null));
        $to = escapeshellarg($this->destination);

        $cmd = "ffmpeg -i {$from} {$outputDimension} {$audioCodec} {$videoCodec} {$rotationIndex} -sn -strict experimental {$to} 2>&1";

        return exec('nice -n 0 ' . $cmd);
    }

    private function outputDimension()
    {
        if ($this->command->outputDimension()) {
            if (preg_match('/^([\d]+)x([\d]+)$/', $this->command->outputDimension())) {
                return $this->command->outputDimension();
            } else {
                throw new Exception\ConversionNotPossible('dimesions for video conversion is incorrect');
            }
        }
        return null;
    }
}
