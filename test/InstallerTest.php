<?php
namespace Barberry\Plugin\Ffmpeg;

use Barberry\ContentType;
use Barberry\Direction;
use Barberry\Direction\Composer as dComposer;
use Barberry\Monitor\Composer as mComposer;

class InstallerTest extends \PHPUnit_Framework_TestCase
{
    private $directionsDir;
    private $monitorsDir;

    protected function setUp()
    {
        $this->directionsDir = realpath(__DIR__ . '/../tmp') . '/test-directions/';
        $this->monitorsDir = realpath(__DIR__ . '/../tmp') . '/test-monitors/';
        @mkdir($this->directionsDir, 0777, true);
        @mkdir($this->monitorsDir, 0777, true);
    }

    protected function tearDown()
    {
        exec('rm -rf ' . $this->directionsDir);
        exec('rm -rf ' . $this->monitorsDir);
    }

    public function testCreatesAviToMp4Direction()
    {
        $installer = new Installer;
        $installer->install(new dComposer($this->directionsDir, '/tmp/'), new mComposer($this->monitorsDir, '/tmp/'));

        include_once $this->directionsDir . 'OgvToMp4.php';
        $direction = new Direction\OgvToMp4Direction('');
        $result = $direction->convert(file_get_contents(__DIR__ . '/data/ogvFile'), Converter::DESTINATION_IS_VIDEO);

        $this->assertEquals(ContentType::mp4(), ContentType::byString($result));
    }
}
