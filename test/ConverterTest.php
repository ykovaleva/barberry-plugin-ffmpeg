<?php
namespace Barberry\Plugin\Videocapture;

use Barberry\ContentType;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertsWebmToMp4() {
        $result = self::converter(ContentType::mp4())->convert(self::bin(), self::command('500x500_a56_v512'));
        $this->assertEquals(ContentType::mp4(), ContentType::byString($result));
    }

    public function testConvertsWebmToAvi() {
        $result = self::converter(ContentType::avi())->convert(self::bin(), self::command('a56_v512_10~10x10'));
        $this->assertEquals(ContentType::avi(), ContentType::byString($result));
    }

    public function testConverts3gpToAvi()
    {
        $bin = file_get_contents(__DIR__ . '/data/3gpFile');
        $result = self::converter(ContentType::avi())->convert($bin, self::command('500x500_a56_v512'));
        $this->assertEquals(ContentType::avi(), ContentType::byString($result));
    }

    public function testConvertsAviToJpeg()
    {
        $bin = file_get_contents(__DIR__ . '/data/aviFile');
        $result = self::converter(ContentType::jpeg())->convert($bin, self::command('124x108_a56_150'));
        $this->assertEquals(ContentType::jpeg(), ContentType::byString($result));
    }

    public function testConvertsWebmToPng()
    {
        $result = self::converter(ContentType::png())->convert(self::bin(), self::command('500x500_a56_v512'));
        $this->assertEquals(ContentType::png(), ContentType::byString($result));
    }

    public function testUtilizesExistingDirectionToExecuteImagemagickCommand()
    {
        require_once __DIR__ . '/FakePngToPngDirection.php';
        self::converter(ContentType::png())->convert(self::bin(), self::command('500x500_a56~50x50'));
        $this->assertTrue(\Barberry\Direction\PngToPngDirection::$hasBeenUtilized);
    }

    public function testThrowsExceptionIfFfmpegFailsToCreateDestinationFile()
    {
        $this->setExpectedException('\\Barberry\\Plugin\\Videocapture\\VideocaptureException');
        self::converter(ContentType::byExtention('3gp'))->convert(self::bin(), self::command('a33_v454'));
    }

    private static function converter($targetContentType)
    {
        return new Converter($targetContentType, __DIR__ . '/../tmp/');
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
