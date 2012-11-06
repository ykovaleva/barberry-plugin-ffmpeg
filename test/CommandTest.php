<?php
namespace Barberry\Plugin\Videocapture;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function testThereAreNoParametersByDefault()
    {
        $this->assertNull(self::command()->screenshotWidth());
        $this->assertNull(self::command()->screenshotHeight());
        $this->assertNull(self::command()->audioBitrate());
        $this->assertNull(self::command()->videoBitrate());
        $this->assertNull(self::command()->screenshotTimestamp());
        $this->assertNull(self::command()->commandForImageMagick());
    }

    public function testExtractsVideoBitrateParameter()
    {
        $this->assertEquals(256, self::command('v256~50x50')->videoBitrate());
    }

    public function testExtractsAudioBitrateParameter()
    {
        $this->assertEquals(128, self::command('a128_v256~50x50')->audioBitrate());
    }

    public function testExtractsScreenshotTimestamp()
    {
        $this->assertEquals(1351844118, self::command('1351844118_v256~50x50')->screenshotTimestamp());
    }

    public function testExtractsScreenshotWidthParameter()
    {
        $this->assertEquals(250, self::command('250x_1351844118_v256~50x50')->screenshotWidth());
    }

    public function testExtractsScreenshotHeightParameter()
    {
        $this->assertEquals(150, self::command('x150_1351844118_v256~50x50')->screenshotHeight());
    }

    public function testExtractsImageMagickCommand()
    {
        $this->assertEquals('800x600', self::command('x150_1351844118_v256~800x600')->commandForImageMagick());
    }

    public function testTransformsParamToMaximumAvailableValues()
    {
        $command = self::command('9000x9000_a512_v4500');
        $this->assertEquals(1280, $command->screenshotWidth());
        $this->assertEquals(720, $command->screenshotHeight());
        $this->assertEquals(256, $command->audioBitrate());
        $this->assertEquals(4000, $command->videoBitrate());
    }

    public function testAmbiguityCommand()
    {
        $this->assertFalse(
            self::command('250x250p_va128_a256_123456_~25x25')->conforms('250x250_v128_a256_1234567890~25x25')
        );
    }

    /**
     * @dataProvider correctCommands
     */
    public function testCorrectCommandsAreConformsToItsTextualRepresentation($commandString)
    {
        $this->assertTrue(self::command($commandString)->conforms($commandString));
    }

    public static function correctCommands()
    {
        return array(
            array('500x500_a128_v256_1351843630~100x100'),
            array('200x_a128_v256'),
            array('~100x100'),
            array('a128_1351843630~100x200')
        );
    }

    private static function command($commandString = '')
    {
        $command = new Command($commandString);
        return $command->configure($commandString);
    }
}
