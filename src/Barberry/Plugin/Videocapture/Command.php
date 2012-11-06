<?php
namespace Barberry\Plugin\Videocapture;

use Barberry\Plugin;

class Command implements Plugin\InterfaceCommand
{
    const MAX_SCREENSHOT_WIDTH = 1280;
    const MAX_SCREENSHOT_HEIGHT = 720;
    const MAX_AUDIO_BITRATE = 256;
    const MAX_VIDEO_BITRATE = 4000;

    private $screenshotWidth;
    private $screenshotHeight;
    private $audioBitrate;
    private $videoBitrate;
    private $screenshotTimestamp;
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
                $this->screenshotWidth = strlen($matches[1]) ? (int)$matches[1] : null;
                $this->screenshotHeight = strlen($matches[2]) ? (int)$matches[2] : null;
            }
            if (preg_match('/^a([\d]+)$/', $parameter, $matches)) {
                $this->audioBitrate = strlen($matches[1]) ? (int)$matches[1] : null;
            }
            if (preg_match('/^v([\d]+)$/', $parameter, $matches)) {
                $this->videoBitrate = strlen($matches[1]) ? (int)$matches[1] : null;;
            }
            if (preg_match('/^[\d]{10}$/', $parameter, $matches)) {
                $this->screenshotTimestamp = (int)$matches[0];
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

    public function screenshotWidth()
    {
        return min($this->screenshotWidth, self::MAX_SCREENSHOT_WIDTH);
    }

    public function screenshotHeight()
    {
        return min($this->screenshotHeight, self::MAX_SCREENSHOT_HEIGHT);
    }

    public function audioBitrate()
    {
        return min($this->audioBitrate, self::MAX_AUDIO_BITRATE);
    }

    public function videoBitrate()
    {
        return min($this->videoBitrate, self::MAX_VIDEO_BITRATE);
    }

    public function screenshotTimestamp()
    {
        return is_null($this->screenshotTimestamp) ? null : $this->screenshotTimestamp;
    }

    public function commandForImagemagick()
    {
        return is_null($this->commandForImagemagick) ? null : $this->commandForImagemagick;
    }

    public function __toString()
    {
        $params = array();

        if ($this->screenshotWidth || $this->screenshotHeight) {
            $params[] = $this->screenshotWidth . 'x' . $this->screenshotHeight;
        }
        if ($this->audioBitrate) {
            $params[] = 'a' . $this->audioBitrate;
        }
        if ($this->videoBitrate) {
            $params[] = 'v' . $this->videoBitrate;
        }
        if ($this->screenshotTimestamp) {
            $params[] = $this->screenshotTimestamp;
        }

        return implode('_', $params) . (is_null($this->commandForImagemagick) ? '' : '~' . $this->commandForImagemagick);
    }
}
