<?php
namespace Barberry\Plugin\Videocapture;

class MonitorTest extends \PHPUnit_Framework_TestCase
{
    public function testReportsIfFfimpegIsNotInstalled()
    {
        $monitor = $this->getMock('Barberry\\Plugin\\Videocapture\\Monitor', array('ffmpegIsInstalled'));
        $monitor->expects($this->once())->method('ffmpegIsInstalled')->will($this->returnValue(false));

        $this->assertContains(
            'Please install ffmpeg',
            $monitor->reportUnmetDependencies()
        );
    }
}
