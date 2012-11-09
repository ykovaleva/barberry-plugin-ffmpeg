<?php
namespace Barberry\Plugin\Ffmpeg;

class FfmpegException extends \Exception
{
    public function __construct($message) {
        parent::__construct('Ffmpeg error: ' . $message, 500);
    }
}
