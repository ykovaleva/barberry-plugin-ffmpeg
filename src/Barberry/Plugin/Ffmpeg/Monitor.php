<?php
namespace Barberry\Plugin\Ffmpeg;

use Barberry\Plugin;

class Monitor implements Plugin\InterfaceMonitor
{
    const FFMPEG_REQUIRED_VERSION = '0.10.5';

    private $tempDir;

    public function configure($tempDir)
    {
        $this->tempDir = $tempDir;
        return $this;
    }

    /**
     * @return array of error messages
     */
    public function reportUnmetDependencies()
    {
        $errors = array();
        if (!$this->ffmpegIsInstalled()) {
            $errors[] = 'Please install ffmpeg';
        }
        if (version_compare($this->ffmpegVersion(), self::FFMPEG_REQUIRED_VERSION, '<')) {
            $errors[] = 'Insufficient ffmpeg version: expected >= 0.10.5';
        }
        return $errors;
    }

    /**
     * @return bool whether ffmpeg is installed
     */
    protected function ffmpegIsInstalled()
    {
        return preg_match('/^\/\w+/', exec("which ffmpeg")) ? true : false;
    }

    /**
     * @return bool whether proper ffmpeg version is installed
     */
    protected function ffmpegVersion()
    {
        $out = exec('ffmpeg -version | grep "ffmpeg version"');
        preg_match('/([\d]+).([\d]+)(.[\d]+)?/', $out, $mathces);
        return $mathces[0];
    }

    /**
     * @return array of error messages
     */
    public function reportMalfunction()
    {
        $report = $this->reportDirectoryIsWritable($this->tempDir);
        return (!is_null($report)) ? array($report) : array();
    }

    private function reportDirectoryIsWritable($dir)
    {
        return (is_writable($dir)) ? null : 'ERROR: Temporary directory is not writable (Ffmpeg plugin)';
    }
}
