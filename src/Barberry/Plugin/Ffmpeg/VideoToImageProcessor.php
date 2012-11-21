<?php
namespace Barberry\Plugin\Ffmpeg;

use Barberry\Direction;
use Barberry\ContentType;

class VideoToImageProcessor implements VideoProcessorInterface
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
        $screenshotTime = $this->command->screenshotTime() ?: $ffmpeg->screenshotTime();
        $rotationIndex = ($this->command->rotation() ? '-vf transpose=' . $this->command->rotation() : ($ffmpeg->rotationIndex() ? '-vf transpose=' . $ffmpeg->rotationIndex() : null));
        $to = escapeshellarg($this->destination);

        $cmd = "ffmpeg -ss {$screenshotTime} -vframes 1 -i {$from} {$rotationIndex} -f image2 {$to} 2>&1";

        exec('nice -n 0 ' . $cmd);

        if ($this->command->outputDimension()) {
            $this->resizeWithImageMagick($this->command->outputDimension());
        }
    }

    private function resizeWithImagemagick($dimension)
    {
        $bin = file_get_contents($this->destination);
        $from = ucfirst(ContentType::byString($bin)->standardExtension());
        $to = ucfirst($this->targetContentType->standardExtension());
        $directionClass = '\\Barberry\\Direction\\' . $from . 'To' . $to . 'Direction';
        $direction = new $directionClass($dimension);
        file_put_contents($this->destination, $direction->convert($bin));
    }
}
