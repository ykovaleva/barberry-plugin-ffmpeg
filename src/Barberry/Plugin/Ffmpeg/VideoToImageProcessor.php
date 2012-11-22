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
    private $defaultParams;

    public function __construct(\Barberry\ContentType $targetContentType)
    {
        $this->targetContentType = $targetContentType;
    }

    public function init($source, $destination, $command)
    {
        $this->source = $source;
        $this->destination = $destination;
        $this->command = $command;
        $this->defaultParams = new FfmpegDefaultParams($this->source, $this->targetContentType);
    }

    public function process()
    {
        $from = escapeshellarg($this->source);
        $to = escapeshellarg($this->destination);

        $cmd = "ffmpeg {$this->screenshotTime()} -vframes 1 -i {$from} {$this->rotationIndex()} -f image2 {$to} 2>&1";
        exec('nice -n 0 ' . $cmd);

        if ($this->command->outputDimension()) {
            $this->resizeWithImageMagick($this->command->outputDimension());
        }
    }

    private function screenshotTime()
    {
        if ($this->command->screenshotTime()) {
            return '-ss ' . $this->command->screenshotTime();
        } else {
            return '-ss ' . $this->defaultParams->screenshotTime();
        }
    }

    private function rotationIndex()
    {
        if ($this->command->rotation()) {
            return '-vf transpose=' . $this->command->rotation();
        } else if ($this->defaultParams->rotationIndex()) {
            return '-vf transpose=' . $this->defaultParams->rotationIndex();
        }
        return null;
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
