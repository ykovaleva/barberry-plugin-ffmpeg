<?php
namespace Barberry\Plugin\Ffmpeg;

use Barberry\ContentType;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertsOgvToFlv()
    {
        $converter = self::converter(ContentType::flv(), Converter::DESTINATION_IS_VIDEO);
        $result = $converter->convert(self::bin('ogv'), self::command('r1'));
        $this->assertEquals(ContentType::flv(), ContentType::byString($result));
    }

    public function testConvertsOgvToWebm()
    {
        $converter = self::converter(ContentType::webm(), Converter::DESTINATION_IS_VIDEO);
        $result = $converter->convert(self::bin('ogv'), self::command(''));
        $this->assertEquals(ContentType::webm(), ContentType::byString($result));
    }

    public function testConvertsOgvToMpeg()
    {
        $converter = self::converter(ContentType::mpeg(), Converter::DESTINATION_IS_VIDEO);
        $result = $converter->convert(self::bin('ogv'), self::command(''));
        $this->assertEquals(ContentType::mpeg(), ContentType::byString($result));
    }

    public function testConvertsWebmToAvi()
    {
        $converter = self::converter(ContentType::avi(), Converter::DESTINATION_IS_VIDEO);
        $result = $converter->convert(self::bin(), self::command(''));
        $this->assertEquals(ContentType::avi(), ContentType::byString($result));
    }

    public function testConvertsOgvToMkv()
    {
        $converter = self::converter(ContentType::mkv(), Converter::DESTINATION_IS_VIDEO);
        $result = $converter->convert(self::bin('ogv'), self::command(''));
        $this->assertEquals(ContentType::mkv(), ContentType::byString($result));
    }

    public function testConvertsOgvToMov()
    {
        $converter = self::converter(ContentType::mov(), Converter::DESTINATION_IS_VIDEO);
        $result = $converter->convert(self::bin('ogv'), self::command(''));
        $this->assertEquals(ContentType::mov(), ContentType::byString($result));
    }

    public function testConvertsWebmToMp4()
    {
        $converter = self::converter(ContentType::mp4(), Converter::DESTINATION_IS_VIDEO);
        $result = $converter->convert(self::bin(), self::command('640x480_r1'));
        $this->assertEquals(ContentType::mp4(), ContentType::byString($result));
    }

    public function testConvertsOgvToMpg()
    {
        $converter = self::converter(ContentType::mpg(), Converter::DESTINATION_IS_VIDEO);
        $result = $converter->convert(self::bin('ogv'), self::command(''));
        $this->assertEquals(ContentType::mpg(), ContentType::byString($result));
    }

    public function testConvertsOgvToOgv()
    {
        $converter = self::converter(ContentType::ogv(), Converter::DESTINATION_IS_VIDEO);
        $result = $converter->convert(self::bin('ogv'), self::command(''));
        $this->assertEquals(ContentType::ogv(), ContentType::byString($result));
    }

    public function testConvertsAviTo3gp()
    {
        $converter = self::converter(ContentType::byExtention('3gp'), Converter::DESTINATION_IS_VIDEO);
        $result = $converter->convert(self::bin('avi'), self::command('176x144'));
        $this->assertEquals(ContentType::byExtention('3gp'), ContentType::byString($result));
    }

    public function testConvertsAviToJpeg()
    {
        $converter = self::converter(ContentType::jpeg(), Converter::DESTINATION_IS_IMAGE);
        $result = $converter->convert(self::bin('avi'), self::command('r1_3'));
        $this->assertEquals(ContentType::jpeg(), ContentType::byString($result));
    }

    public function testConvertsWebmToPng()
    {
        $converter = self::converter(ContentType::png(), Converter::DESTINATION_IS_IMAGE);
        $result = $converter->convert(self::bin(), self::command(''));
        $this->assertEquals(ContentType::png(), ContentType::byString($result));
    }

    public function testConvertsMp4ToPng()
    {
        $converter = self::converter(ContentType::png(), Converter::DESTINATION_IS_IMAGE);
        $result = $converter->convert(self::bin('mp4'), self::command('vc:libx264'));
        $this->assertEquals(ContentType::png(), ContentType::byString($result));
    }

    public function testThrowsExceptionIfFfmpegFailsToCreateDestinationFile()
    {
        $converter = self::converter(ContentType::byExtention('3gp'), Converter::DESTINATION_IS_VIDEO);
        $this->setExpectedException('\\Barberry\\Exception\\ConversionNotPossible');
        $converter->convert(self::bin(), self::command('vc:454'));
    }

    private static function converter($targetContentType, $directionType)
    {
        $converter = new Converter($directionType);
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

    private static function bin($extension = 'webm')
    {
        return file_get_contents(__DIR__ . "/data/{$extension}File");
    }
}
