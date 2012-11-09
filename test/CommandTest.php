<?php
namespace Barberry\Plugin\Ffmpeg;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function testThereAreNoParametersByDefault()
    {
        $this->assertNull(self::command()->outputDimension());
        $this->assertNull(self::command()->audioCodec());
        $this->assertNull(self::command()->audioBitrate());
        $this->assertNull(self::command()->videoCodec());
        $this->assertNull(self::command()->videoBitrate());
        $this->assertNull(self::command()->rotation());
        $this->assertNull(self::command()->screenshotTime());
    }

    public function testExtractsOutputDimensionParameter()
    {
        $this->assertEquals('100x100', self::command('100x100_ab128_vb256')->outputDimension());
        $this->assertEquals('105x', self::command('105x_ab128_vb256')->outputDimension());
    }

    public function testExtractsAudioBitrateParameter()
    {
        $this->assertEquals(128, self::command('110x_ab128_vb256')->audioBitrate());
    }

    public function testExtractsAudioCodecParameter()
    {
        $this->assertEquals('aac', self::command('ab128_ac:aac_vb256')->audioCodec());
    }

    public function testExtractsVideoBitrateParameter()
    {
        $this->assertEquals(256, self::command('vb256')->videoBitrate());
    }

    public function testExtractsVideoCodecParameter()
    {
        $this->assertEquals('h263', self::command('ab128_vc:h263_vb256')->videoCodec());
    }

    public function testExtractsRotationParameter()
    {
        $this->assertEquals(2, self::command('vb256_r2')->rotation());
    }

    public function testExtractsScreenshotTime()
    {
        $this->assertEquals(135, self::command('vb256_135')->screenshotTime());
    }

    public function testTransformsParamToMaximumAvailableValues()
    {
        $command = self::command('9000x9000_ab512_vb4500_r180');
        $this->assertEquals(256, $command->audioBitrate());
        $this->assertEquals(4000, $command->videoBitrate());
        $this->assertEquals(3, $command->rotation());
    }

    public function testAmbiguityCommand()
    {
        $this->assertFalse(
            self::command('25xx25_va128_a256_123456')->conforms('25x25_vb128_ab256_123456')
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
            array('100x100_ab128_vb256_135'),
            array('ab128_ac:mp3_vb512_vc:h263_10'),
            array('100x100'),
            array('ab128_135')
        );
    }

    private static function command($commandString = '')
    {
        $command = new Command($commandString);
        return $command->configure($commandString);
    }
}
