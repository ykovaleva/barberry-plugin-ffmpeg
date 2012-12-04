<?php
namespace Barberry\Plugin\Ffmpeg;

use Barberry\ContentType;

class VideoToImageProcessorTest extends \PHPUnit_Framework_TestCase
{
    private $source;
    private $destination;

    protected function setUp()
    {
        $this->source = __DIR__ . '/data/mp4File';
        $this->destination = __DIR__ . '/../tmp/testProcessor.png';
    }

    protected function tearDown()
    {
        unlink($this->destination);
    }

    public function testProcessVideoToImage()
    {
        $this->processor(self::command())->process();
        $this->assertEquals(
            ContentType::byString(file_get_contents($this->destination)),
            ContentType::png()
        );
    }

    public function testProcessVideoToImageInvolvingImagemagick()
    {
        $mock = $this->getMock(
            '\\Barberry\\Plugin\\Ffmpeg\\VideoToImageProcessor',
            array('resizeWithImagemagick'),
            array(ContentType::png())
        );
        $mock->expects($this->once())
             ->method('resizeWithImagemagick');

        $mock->init($this->source, $this->destination, self::command('100x100'));
        $mock->process();
    }

    private function processor($command = null)
    {
        $command = $command ? $command : self::command();
        $processor = new VideoToImageProcessor(ContentType::png());
        $processor->init($this->source, $this->destination, $command);
        return $processor;
    }

    private static function command($commandString = null)
    {
        $commandString = $commandString ? $commandString : '';
        $command = new Command;
        $command->configure($commandString);
        if ($command->conforms($commandString)) {
            return $command;
        }
    }
}
