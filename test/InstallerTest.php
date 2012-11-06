<?php
namespace Barberry\Plugin\Videocapture;

use Barberry\ContentType;
use Mockery as m;

class InstallerTest extends \PHPUnit_Framework_TestCase
{
    public function testDelegatesCreationOfVideocaptureDirections()
    {
        $composer = m::mock('Barberry\\Direction\\ComposerInterface');

        foreach(Installer::directions() as $direction) {
            $composer->shouldReceive('writeClassDeclaration')->with(
                equalTo($direction[0]), equalTo(eval('return ' . $direction[1] . ';')), anything(), anything()
            )->once();
        }

        $installer = new Installer();
        $installer->install($composer, $this->getMock('Barberry\\Monitor\\ComposerInterface'));
    }

    public function testDelegatesCreationOfVideocaptureMonitor()
    {
        $composer = $this->getMock('Barberry\\Monitor\\ComposerInterface');
        $composer->expects($this->once())->method('writeClassDeclaration')->with(
            'Videocapture',
            $this->anything()
        );

        $installer = new Installer();
        $installer->install($this->getMock('Barberry\\Direction\\ComposerInterface'), $composer);
    }
}
