<?php
namespace Barberry\Plugin\Videocapture;

use Barberry\Plugin;

class Monitor implements Plugin\InterfaceMonitor
{
    /**
     * @return array of error messages
     */
    public function reportUnmetDependencies()
    {
        $errors = array();
        if (!$this->ffmpegIsInstalled()) {
            $errors[] = 'Please install ffmpeg';
        }
        return $errors;
    }

    /**
     * @return array of error messages
     */
    public function reportMalfunction()
    {
        // TODO: Implement reportMalfunction() method.
    }

    /**
     * @return bool whether ffmpeg is installed
     */
    protected function ffmpegIsInstalled()
    {
        return preg_match('/^\/\w+/', exec("which ffmpeg")) ? true : false;
    }
}
