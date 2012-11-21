<?php
namespace Barberry\Plugin\Ffmpeg;

use Barberry\ContentType;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertsOgvToFlv()
    {
        $result = self::converter(ContentType::flv(), \Barberry\Plugin\Ffmpeg\Converter::DESTINATION_IS_VIDEO)->convert(self::bin('ogv'), self::command('r1'));
        $this->assertEquals(ContentType::flv(), ContentType::byString($result));
    }

    public function testConvertsOgvToWebm()
    {
        $result = self::converter(ContentType::webm(), \Barberry\Plugin\Ffmpeg\Converter::DESTINATION_IS_VIDEO)->convert(self::bin('ogv'), self::command('100x100'));
        $this->assertEquals(ContentType::webm(), ContentType::byString($result));
    }

    public function testConvertsOgvToMpeg()
    {
        $result = self::converter(ContentType::mpeg(), \Barberry\Plugin\Ffmpeg\Converter::DESTINATION_IS_VIDEO)->convert(self::bin('ogv'), self::command(''));
        $this->assertEquals(ContentType::mpeg(), ContentType::byString($result));
    }

    public function testConvertsWebmToAvi()
    {
        $result = self::converter(ContentType::avi(), \Barberry\Plugin\Ffmpeg\Converter::DESTINATION_IS_VIDEO)->convert(self::bin(), self::command(''));
        $this->assertEquals(ContentType::avi(), ContentType::byString($result));
    }

    public function testConvertsOgvToMkv()
    {
        $result = self::converter(ContentType::mkv(), \Barberry\Plugin\Ffmpeg\Converter::DESTINATION_IS_VIDEO)->convert(self::bin('ogv'), self::command(''));
        $this->assertEquals(ContentType::mkv(), ContentType::byString($result));
    }

    public function testConvertsOgvToMov()
    {
        $result = self::converter(ContentType::mov(), \Barberry\Plugin\Ffmpeg\Converter::DESTINATION_IS_VIDEO)->convert(self::bin('ogv'), self::command(''));
        $this->assertEquals(ContentType::mov(), ContentType::byString($result));
    }

    public function testConvertsWebmToMp4()
    {
        $result = self::converter(ContentType::mp4(), \Barberry\Plugin\Ffmpeg\Converter::DESTINATION_IS_VIDEO)->convert(self::bin(), self::command('640x480_r1'));
        $this->assertEquals(ContentType::mp4(), ContentType::byString($result));
    }

    public function testConvertsOgvToMpg()
    {
        $result = self::converter(ContentType::mpg(), \Barberry\Plugin\Ffmpeg\Converter::DESTINATION_IS_VIDEO)->convert(self::bin('ogv'), self::command(''));
        $this->assertEquals(ContentType::mpg(), ContentType::byString($result));
    }

    public function testConvertsOgvToOgv()
    {
        $result = self::converter(ContentType::ogv(), \Barberry\Plugin\Ffmpeg\Converter::DESTINATION_IS_VIDEO)->convert(self::bin('ogv'), self::command(''));
        $this->assertEquals(ContentType::ogv(), ContentType::byString($result));
    }

    public function testConvertsAviTo3gp()
    {
        $result = self::converter(ContentType::byExtention('3gp'), \Barberry\Plugin\Ffmpeg\Converter::DESTINATION_IS_VIDEO)->convert(self::bin('avi'), self::command('176x144'));
        $this->assertEquals(ContentType::byExtention('3gp'), ContentType::byString($result));
    }

    public function testConvertsAviToJpeg()
    {
        $result = self::converter(ContentType::jpeg(), \Barberry\Plugin\Ffmpeg\Converter::DESTINATION_IS_IMAGE)->convert(self::bin('avi'), self::command('r1_3'));
        $this->assertEquals(ContentType::jpeg(), ContentType::byString($result));
    }

    public function testConvertsWebmToPng()
    {
        $result = self::converter(ContentType::png(), \Barberry\Plugin\Ffmpeg\Converter::DESTINATION_IS_IMAGE)->convert(self::bin(), self::command(''));
        $this->assertEquals(ContentType::png(), ContentType::byString($result));
    }

    public function testConvertsMp4ToPng() {
        $result = self::converter(ContentType::png(), \Barberry\Plugin\Ffmpeg\Converter::DESTINATION_IS_IMAGE)->convert(self::bin('mp4'), self::command('vc:libx264'));
        $this->assertEquals(ContentType::png(), ContentType::byString($result));
    }

    public function testThrowsExceptionIfFfmpegFailsToCreateDestinationFile()
    {
        $this->setExpectedException('\\Barberry\\Exception\\ConversionNotPossible');
        self::converter(ContentType::byExtention('3gp'), \Barberry\Plugin\Ffmpeg\Converter::DESTINATION_IS_VIDEO)->convert(self::bin(), self::command('vc:454'));
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
