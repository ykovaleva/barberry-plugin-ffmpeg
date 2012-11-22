<?php
namespace Barberry\Plugin\Ffmpeg;

use Barberry\ContentType;

class FfmpegDefaultParamsTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractsRotationParameterFromFileMetaData()
    {
        $this->assertEquals(1, self::defaultParams()->rotationIndex());
    }

    public function testGetsTimestampParameter()
    {
        $this->assertGreaterThan(0, self::defaultParams()->screenshotTime());
    }

    public function testDefinesDefaultAudioCodec()
    {
        $this->assertEquals('libvorbis', self::defaultParams()->audioCodec());
    }

    public function testDefinesDefaultVideoCodec()
    {
        $this->assertEquals('libtheora', self::defaultParams()->videoCodec());
    }

    private static function defaultParams()
    {
        return new FfmpegDefaultParams(__DIR__ . '/data/mp4File', ContentType::mkv());
    }
}
