<?php
namespace Barberry\Plugin\Ffmpeg;

use Barberry\ContentType;

class VideoToVideoProcessorTest extends \PHPUnit_Framework_TestCase
{
    private $source;
    private $destination;

    protected function setUp()
    {
        $this->source = __DIR__ . '/data/mp4File';
        $this->destination = __DIR__ . '/../tmp/testProcessor.mp4';
    }

    protected function tearDown()
    {
        unlink($this->destination);
    }

    public function testProcessVideoToVideo()
    {
        $this->processor()->process();
        $this->assertEquals(
            ContentType::byString(file_get_contents($this->destination)),
            ContentType::mp4()
        );
    }

    private function processor()
    {
        $processor = new VideoToVideoProcessor(ContentType::mp4());
        $processor->init($this->source, $this->destination, self::command());
        return $processor;
    }

    private static function command()
    {
        $command = new Command;
        $command->configure('');
        if ($command->conforms('')) {
            return $command;
        }
    }
}
