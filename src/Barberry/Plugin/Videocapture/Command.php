<?php
/**
 * Command class file, explodes input string to parameters for ffmpeg.
 *
 * Available command string (any parameter may be omitted, but their order is important):
 * ab128_ac:mp3_vb256_vc:h263_r1_10~100x100
 *
 * ab128 - audio bitrate
 * ac:mp3 - audio codec
 * vb256 - video bitrate
 * vc:h263 - video codec
 * r1 - rotation parameter (
 *     available values:
 *     0 - default position. 1 - rotate 90 degree, 2 - 180 degree, 3 - 270 degree
 * )
 * last numeric parameter (10) - on what second make screenshot.
 * Anything after '~' sign will be send to imagemagick barberry plugin as command string.
 */

namespace Barberry\Plugin\Videocapture;

use Barberry\Plugin;

class Command implements Plugin\InterfaceCommand
{
    const MAX_AUDIO_BITRATE = 256;
    const MAX_VIDEO_BITRATE = 4000;
    const MAX_ROTATION = 3;

    private $audioBitrate;
    private $audioCodec;
    private $videoBitrate;
    private $videoCodec;
    private $rotation;
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
            if (preg_match('/^ab([\d]+)$/', $parameter, $matches)) {
                $this->audioBitrate = strlen($matches[1]) ? (int)$matches[1] : null;
            }
            if (preg_match('/^ac:([\w]+)$/', $parameter, $matches)) {
                $this->audioCodec = strlen($matches[1]) ? $matches[1] : null;;
            }
            if (preg_match('/^vb([\d]+)$/', $parameter, $matches)) {
                $this->videoBitrate = strlen($matches[1]) ? (int)$matches[1] : null;;
            }
            if (preg_match('/^vc:([\w]+)$/', $parameter, $matches)) {
                $this->videoCodec = strlen($matches[1]) ? $matches[1] : null;;
            }
            if (preg_match('/^r([\d]+)$/', $parameter, $matches)) {
                $this->rotation = strlen($matches[1]) ? (int)$matches[1] : null;;
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

    public function audioBitrate()
    {
        return min($this->audioBitrate, self::MAX_AUDIO_BITRATE);
    }

    public function audioCodec()
    {
        return is_null($this->audioCodec) ? null : $this->audioCodec;
    }

    public function videoBitrate()
    {
        return min($this->videoBitrate, self::MAX_VIDEO_BITRATE);
    }

    public function videoCodec()
    {
        return is_null($this->videoCodec) ? null : $this->videoCodec;
    }

    public function rotation()
    {
        return min($this->rotation, self::MAX_ROTATION);
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

        if ($this->audioBitrate) {
            $params[] = 'ab' . $this->audioBitrate;
        }
        if ($this->audioCodec) {
            $params[] = 'ac:' . $this->audioCodec;
        }
        if ($this->videoBitrate) {
            $params[] = 'vb' . $this->videoBitrate;
        }
        if ($this->videoCodec) {
            $params[] = 'vc:' . $this->videoCodec;
        }
        if ($this->rotation) {
            $params[] = 'r' . $this->rotation;
        }
        if ($this->screenshotTime) {
            $params[] = $this->screenshotTime;
        }
        return implode('_', $params) . (is_null($this->commandForImagemagick) ? '' : '~' . $this->commandForImagemagick);
    }
}
