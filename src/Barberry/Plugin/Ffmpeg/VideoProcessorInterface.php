<?php
namespace Barberry\Plugin\Ffmpeg;

interface VideoProcessorInterface
{
    public function init($sourceFile, $destinationFile, $command);

    public function process();
}
