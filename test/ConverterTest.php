<?php
namespace Barberry\Plugin\Ffmpeg;

use Barberry\ContentType;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertsWebmToMp4() {
        $result = self::converter(ContentType::mp4())->convert(self::bin(), self::command('ab56_vc:mpeg4_r1'));
        $this->assertEquals(ContentType::mp4(), ContentType::byString($result));
    }

    public function testConvertsWebmToAvi() {
        $result = self::converter(ContentType::avi())->convert(self::bin(), self::command('ab56'));
        $this->assertEquals(ContentType::avi(), ContentType::byString($result));
    }

    public function testConverts3gpToAvi()
    {
        $bin = file_get_contents(__DIR__ . '/data/3gpFile');
        $result = self::converter(ContentType::avi())->convert($bin, self::command('ab56_vb512'));
        $this->assertEquals(ContentType::avi(), ContentType::byString($result));
    }

    public function testConvertsAviTo3gp()
    {
        $bin = file_get_contents(__DIR__ . '/data/aviFile');
        $result = self::converter(ContentType::byExtention('3gp'))->convert($bin, self::command('176x144_ac:aac_vc:h263'));
        $this->assertEquals(ContentType::byExtention('3gp'), ContentType::byString($result));
    }

    public function testConvertsAviToJpeg()
    {
        $bin = file_get_contents(__DIR__ . '/data/aviFile');
        $result = self::converter(ContentType::jpeg())->convert($bin, self::command('r2_150'));
        $this->assertEquals(ContentType::jpeg(), ContentType::byString($result));
    }

    public function testConvertsWebmToPng()
    {
        $result = self::converter(ContentType::png())->convert(self::bin(), self::command('ab56_vb512'));
        $this->assertEquals(ContentType::png(), ContentType::byString($result));
    }

    public function testUtilizesExistingDirectionToExecuteImagemagickCommandForResizing()
    {
        require_once __DIR__ . '/FakePngToPngDirection.php';
        self::converter(ContentType::png())->convert(self::bin(), self::command('50x50_ab56'));
        $this->assertTrue(\Barberry\Direction\PngToPngDirection::$hasBeenUtilized);
    }

    public function testThrowsExceptionIfFfmpegFailsToCreateDestinationFile()
    {
        $this->setExpectedException('\\Barberry\\Plugin\\Ffmpeg\\FfmpegException');
        self::converter(ContentType::byExtention('3gp'))->convert(self::bin(), self::command('ab33_vb454'));
    }

    private static function converter($targetContentType)
    {
        $converter = new Converter;
        return $converter->configure($targetContentType, __DIR__ . '/../tmp/');
    }

    private static function command($commandString)
    {
        $command = new Command();
        $command->configure($commandString);
        if ($command->conforms($commandString)) {
            return $command;
        }
    }

    private static function bin()
    {
        return file_get_contents(__DIR__ . '/data/webmFile');
    }
}
