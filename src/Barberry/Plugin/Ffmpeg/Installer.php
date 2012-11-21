<?php
namespace Barberry\Plugin\Ffmpeg;

use Barberry\Plugin;
use Barberry\Monitor;
use Barberry\Direction;
use Barberry\ContentType;

class Installer implements Plugin\InterfaceInstaller
{
    public function install(Direction\ComposerInterface $directionComposer, Monitor\ComposerInterface $monitorComposer,
                            $pluginParams = array())
    {
        foreach (self::videoToVideoDirections() as $pair) {
            $this->declareDirection($directionComposer, $pair, 'Barberry\Plugin\Ffmpeg\Converter::DESTINATION_IS_VIDEO');
        }
        foreach (self::videoToImageDirections() as $pair) {
            $this->declareDirection($directionComposer, $pair, 'Barberry\Plugin\Ffmpeg\Converter::DESTINATION_IS_IMAGE');
        }
        $monitorComposer->writeClassDeclaration('Ffmpeg');
    }

    private static function videoToVideoDirections()
    {
        $directions = array();
        foreach (self::supportedVideoFormats() as $source) {
            foreach (self::supportedVideoFormats() as $destination) {
                $directions[] = array(
                    \Barberry\ContentType::byExtention($source),
                    \Barberry\ContentType::byExtention($destination)
                );
            }
        }
        return $directions;
    }

    private static function videoToImageDirections()
    {
        $directions = array();
        foreach (self::supportedVideoFormats() as $source) {
            foreach (self::supportedImageFormats() as $destination) {
                $directions[] = array(
                    \Barberry\ContentType::byExtention($source),
                    \Barberry\ContentType::byExtention($destination)
                );
            }
        }
        return $directions;
    }

    private function declareDirection(Direction\ComposerInterface $directionComposer, $directions, $type)
    {
        $directionComposer->writeClassDeclaration(
            $directions[0],
            $directions[1],
            "new Plugin\\Ffmpeg\\Converter({$type})",
            'new Plugin\\Ffmpeg\\Command'
        );
    }

    private static function supportedVideoFormats()
    {
        return array('flv', 'webm', 'wmv', 'mpeg', 'avi', 'mkv', 'mov', 'mp4', 'mpg', 'ogv', '3gp');
    }

    private static function supportedImageFormats()
    {
        return array('png', 'jpeg');
    }
}
