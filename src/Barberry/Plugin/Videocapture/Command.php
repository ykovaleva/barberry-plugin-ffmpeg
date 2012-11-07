<?php
namespace Barberry\Plugin\Videocapture;

use Barberry\Plugin;

class Command implements Plugin\InterfaceCommand
{
    const MAX_WIDTH = 1280; // output resolution either video or image
    const MAX_HEIGHT = 720;
    const MAX_AUDIO_BITRATE = 256;
    const MAX_VIDEO_BITRATE = 4000;

    private $width;
    private $height;
    private $audioBitrate;
    private $videoBitrate;
    private $screenshotTime;
    private $commandForImagemagick;

    /**
     * @param string $commandString
     * @return InterfaceCommand
     */
    public function configure($commandString)
    {
        $commands = explode('~', $commandString);
        $params = explode('_', $commands[0]);

        foreach ($params as $parameter) {
            if (preg_match('/^([\d]*)x([\d]*)$/', $parameter, $matches)) {
                $this->width = strlen($matches[1]) ? (int)$matches[1] : null;
                $this->height = strlen($matches[2]) ? (int)$matches[2] : null;
            }
            if (preg_match('/^a([\d]+)$/', $parameter, $matches)) {
                $this->audioBitrate = strlen($matches[1]) ? (int)$matches[1] : null;
            }
            if (preg_match('/^v([\d]+)$/', $parameter, $matches)) {
                $this->videoBitrate = strlen($matches[1]) ? (int)$matches[1] : null;;
            }
            if (preg_match('/^([\d]+)$/', $parameter, $matches)) {
                $this->screenshotTime = (int)$matches[1];
            }
        }

        $this->commandForImagemagick = isset($commands[1]) ? $commands[1] : null;

        return $this;
    }

    /**
     * Command should have only one string representation
     *
     * @param string $commandString
     * @return boolean
     */
    public function conforms($commandString)
    {
        return strval($this) === $commandString;
    }

    public function width()
    {
        return min($this->width, self::MAX_WIDTH);
    }

    public function height()
    {
        return min($this->height, self::MAX_HEIGHT);
    }

    public function audioBitrate()
    {
        return min($this->audioBitrate, self::MAX_AUDIO_BITRATE);
    }

    public function videoBitrate()
    {
        return min($this->videoBitrate, self::MAX_VIDEO_BITRATE);
    }

    public function screenshotTime()
    {
        return is_null($this->screenshotTime) ? null : $this->screenshotTime;
    }

    public function commandForImagemagick()
    {
        return is_null($this->commandForImagemagick) ? null : $this->commandForImagemagick;
    }

    public function __toString()
    {
        $params = array();

        if ($this->width || $this->height) {
            $params[] = $this->width . 'x' . $this->height;
        }
        if ($this->audioBitrate) {
            $params[] = 'a' . $this->audioBitrate;
        }
        if ($this->videoBitrate) {
            $params[] = 'v' . $this->videoBitrate;
        }
        if ($this->screenshotTime) {
            $params[] = $this->screenshotTime;
        }
        return implode('_', $params) . (is_null($this->commandForImagemagick) ? '' : '~' . $this->commandForImagemagick);
    }
}
