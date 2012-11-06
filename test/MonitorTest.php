<?php
namespace Barberry\Plugin\Videocapture;

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
        $monitor = $this->getMock('Barberry\\Plugin\\Videocapture\\Monitor', array('ffmpegIsInstalled'));
        $monitor->expects($this->once())->method('ffmpegIsInstalled')->will($this->returnValue(false));

        $this->assertContains(
            'Please install ffmpeg',
            $monitor->reportUnmetDependencies()
        );
    }

    public function testReportsNoErrorsIfDirectoryIsWritable()
    {
        $monitor = new Monitor($this->testDirWritable);
        $this->assertEquals(array(), $monitor->reportMalfunction());
    }

    public function testReportsErrorsIfDirectoryIsNotWritable()
    {
        $monitor = new Monitor($this->testDirNotWritable);
        $this->assertEquals(
            array(
                'ERROR: Temporary directory is not writable (Videocapture plugin)'
            ),
            $monitor->reportMalfunction()
        );
    }
}
