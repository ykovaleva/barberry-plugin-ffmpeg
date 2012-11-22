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

namespace Barberry\Plugin\Ffmpeg;

use Barberry\Plugin;

class Command implements Plugin\InterfaceCommand
{
    const MAX_WIDTH = 1280;
    const MAX_HEIGHT = 720;
    const MAX_ROTATION = 3;

    private $width;
    private $height;
    private $audioCodec;
    private $videoCodec;
    private $rotation;
    private $screenshotTime;

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
            if (preg_match('/^ac:([\w]+)$/', $parameter, $matches)) {
                $this->audioCodec = strlen($matches[1]) ? $matches[1] : null;;
            }
            if (preg_match('/^vc:([\w]+)$/', $parameter, $matches)) {
                $this->videoCodec = strlen($matches[1]) ? $matches[1] : null;;
            }
            if (preg_match('/^r([\d]+)$/', $parameter, $matches)) {
                $this->rotation = (int)$matches[1] ?: null;
            }
            if (preg_match('/^([\d]+)$/', $parameter, $matches)) {
                $this->screenshotTime = (int)$matches[1];
            }
        }
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

    public function outputDimension()
    {
        $width = min($this->width, self::MAX_WIDTH);
        $height = min($this->height, self::MAX_HEIGHT);
        if ($width || $height) {
            return $width . 'x' . $height;
        }
        return null;
    }

    public function audioCodec()
    {
        return is_null($this->audioCodec) ? null : $this->audioCodec;
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

    public function __toString()
    {
        $params = array();

        if ($this->width || $this->height) {
            $params[] = $this->width . 'x' . $this->height;
        }
        if ($this->audioCodec) {
            $params[] = 'ac:' . $this->audioCodec;
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
        return implode('_', $params);
    }
}
