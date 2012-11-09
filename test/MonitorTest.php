<?php
namespace Barberry\Plugin\Ffmpeg;

class MonitorTest extends \PHPUnit_Framework_TestCase
{
    private $testDirWritable;
    private $testDirNotWritable;

    protected function setUp()
    {
        $path = realpath(__DIR__) . '/../tmp';

        $this->testDirWritable = $path . '/testdir-writable/';
        $this->testDirNotWritable = $path . '/testdir-notwritable/';

        @mkdir($this->testDirWritable, 0777, true);
        @mkdir($this->testDirNotWritable, 0444, true);
    }

    protected function tearDown()
    {
        exec('rm -rf ' . $this->testDirWritable);
        exec('rm -rf ' . $this->testDirNotWritable);
    }
    public function testReportsIfFfimpegIsNotInstalled()
    {
        $monitor = $this->getMock('Barberry\\Plugin\\Ffmpeg\\Monitor', array('ffmpegIsInstalled'));
        $monitor->expects($this->once())->method('ffmpegIsInstalled')->will($this->returnValue(false));

        $this->assertContains(
            'Please install ffmpeg',
            $monitor->reportUnmetDependencies()
        );
    }

    public function testReportsNoErrorsIfDirectoryIsWritable()
    {
        $monitor = self::monitor($this->testDirWritable);
        $this->assertEquals(array(), $monitor->reportMalfunction());
    }

    public function testReportsErrorsIfDirectoryIsNotWritable()
    {
        $monitor = self::monitor($this->testDirNotWritable);
        $this->assertEquals(
            array(
                'ERROR: Temporary directory is not writable (Ffmpeg plugin)'
            ),
            $monitor->reportMalfunction()
        );
    }

    private static function monitor($tempDir)
    {
        $monitor = new Monitor;
        return $monitor->configure($tempDir);
    }
}
