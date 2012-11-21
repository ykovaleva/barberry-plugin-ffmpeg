<?php
namespace Barberry\Plugin\Ffmpeg;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function testThereAreNoParametersByDefault()
    {
        $this->assertNull(self::command()->outputDimension());
        $this->assertNull(self::command()->audioCodec());
        $this->assertNull(self::command()->videoCodec());
        $this->assertNull(self::command()->rotation());
        $this->assertNull(self::command()->screenshotTime());
    }

    public function testExtractsOutputDimensionParameter()
    {
        $this->assertEquals('100x100', self::command('100x100')->outputDimension());
        $this->assertEquals('105x', self::command('105x_')->outputDimension());
    }

    public function testExtractsAudioCodecParameter()
    {
        $this->assertEquals('aac', self::command('ac:aac')->audioCodec());
    }

    public function testExtractsVideoCodecParameter()
    {
        $this->assertEquals('h263', self::command('vc:h263')->videoCodec());
    }

    public function testExtractsRotationParameter()
    {
        $this->assertEquals(2, self::command('r2')->rotation());
    }

    public function testExtractsScreenshotTime()
    {
        $this->assertEquals(135, self::command('135')->screenshotTime());
    }

    public function testTransformsParamToMaximumAvailableValues()
    {
        $command = self::command('9000x9000_r180');
        $this->assertEquals(3, $command->rotation());
    }

    public function testAmbiguityCommand()
    {
        $this->assertFalse(
            self::command('25xx25_ac:aac_123456')->conforms('25x25_ac:aac_123456')
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
            array('100x100_ac:aac_135'),
            array('ac:mp3_vc:h263_10'),
            array('100x100'),
            array('135')
        );
    }

    private static function command($commandString = '')
    {
        $command = new Command($commandString);
        return $command->configure($commandString);
    }
}
