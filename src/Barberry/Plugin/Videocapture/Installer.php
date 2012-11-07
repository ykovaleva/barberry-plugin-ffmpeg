<?php
namespace Barberry\Plugin\Videocapture;

use Barberry\Plugin;
use Barberry\Monitor;
use Barberry\Direction;
use Barberry\ContentType;

class Installer implements Plugin\InterfaceInstaller
{
    private $tempDir;

    public function __construct($dir)
    {
        $this->tempDir = $dir;
    }

    public function install(Direction\ComposerInterface $directionComposer, Monitor\ComposerInterface $monitorComposer,
                            $pluginParams = array())
    {
        foreach ($this->directions() as $pair) {
            $directionComposer->writeClassDeclaration(
                $pair[0],
                eval('return ' . $pair[1] . ';'),
                <<<PHP
new Plugin\\Videocapture\\Converter ($pair[1], '{$this->tempDir}');
PHP
                ,
                'new Plugin\\Videocapture\\Command'
            );
        }

        $monitorComposer->writeClassDeclaration('Videocapture', "parent::__construct('{$this->tempDir}'')");
    }

    /**
    * @return array with available convertation directions.
    * Be aware: format qt is not supported by ffmpeg.
    */
    public static function directions()
    {
        $supportedVideo = array('flv', 'webm', 'wmv', 'mpeg', 'avi', 'mkv', 'mov', 'mp4', 'mpg', 'ogv');
        $supportedImage = array('png', 'jpeg');

        $directions = array();
        foreach ($supportedVideo as $source) {
            foreach ($supportedImage as $destinationImage) {
                $directions[] = array(
                    call_user_func(array('\\Barberry\\ContentType', $source)), '\\Barberry\\ContentType::' . $destinationImage . '()'
                );
            }
            foreach ($supportedVideo as $destinationVideo) {
                $directions[] = array(
                    call_user_func(array('\\Barberry\\ContentType', $source)), '\\Barberry\\ContentType::' . $destinationVideo . '()'
                );
            }
        }

        // directions for 3gp format
        foreach ($supportedVideo as $video) {
            $directions[] = array(ContentType::byExtention('3gp'), '\\Barberry\\ContentType::' . $video . '()');
            $directions[] = array(
                call_user_func(array('\\Barberry\\ContentType', $video)), '\\Barberry\\ContentType::byExtention("3gp")'
            );
        }
        foreach ($supportedImage as $image) {
            $directions[] = array(ContentType::byExtention('3gp'), '\\Barberry\\ContentType::' . $image . '()');
        }

        return $directions;
    }
}
