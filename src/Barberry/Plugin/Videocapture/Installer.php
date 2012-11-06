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
     * Be aware: formats wmv, mkv, mpg, qt, ogv, are not supported by ffmpeg.
     */
    public static function directions()
    {
        return array(
            array(ContentType::flv(), '\\Barberry\\ContentType::png()'),
            array(ContentType::webm(), '\\Barberry\\ContentType::png()'),
            array(ContentType::mpeg(), '\\Barberry\\ContentType::png()'),
            array(ContentType::avi(), '\\Barberry\\ContentType::png()'),
            array(ContentType::mp4(), '\\Barberry\\ContentType::png()'),
            array(ContentType::mov(), '\\Barberry\\ContentType::png()'),
            array(ContentType::byExtention('3gp'), '\\Barberry\\ContentType::png()')
        );
    }
}
