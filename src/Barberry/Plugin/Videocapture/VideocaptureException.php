<?php
namespace Barberry\Plugin\Videocapture;

class VideocaptureException extends \Exception
{
    public function __construct($message) {
        parent::__construct('Videocapture error: ' . $message, 500);
    }
}
