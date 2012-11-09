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
        foreach ($this->directions() as $pair) {
            $directionComposer->writeClassDeclaration(
                $pair[0],
                $pair[1],
                'new Plugin\\Ffmpeg\\Converter',
                'new Plugin\\Ffmpeg\\Command'
            );
        }

        $monitorComposer->writeClassDeclaration('Ffmpeg');
    }

    /**
    * @return array with available convertation directions.
    * Be aware: format qt is not supported by ffmpeg.
    */
    public static function directions()
    {
        $supportedVideo = array('flv', 'webm', 'wmv', 'mpeg', 'avi', 'mkv', 'mov', 'mp4', 'mpg', 'ogv', '3gp');
        $supportedImage = array('png', 'jpeg');

        $directions = array();
        foreach ($supportedVideo as $source) {
            foreach ($supportedImage as $destinationImage) {
                $directions[] = array(
                    \Barberry\ContentType::byExtention($source),
                    \Barberry\ContentType::byExtention($destinationImage)
                );
            }
            foreach ($supportedVideo as $destinationVideo) {
                $directions[] = array(
                    \Barberry\ContentType::byExtention($source),
                    \Barberry\ContentType::byExtention($destinationVideo)
                );
            }
        }

        return $directions;
    }
}
