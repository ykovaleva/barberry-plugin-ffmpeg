<?php
namespace Barberry\Plugin\Videocapture;

use Barberry\Plugin;

class Command implements Plugin\InterfaceCommand
{

    /**
     * @param string $commandString
     * @return InterfaceCommand
     */
    public function configure($commandString)
    {
        // TODO: Implement configure() method.
    }

    /**
     * Command should have only one string representation
     *
     * @param string $commandString
     * @return boolean
     */
    public function conforms($commandString)
    {
        // TODO: Implement conforms() method.
    }
}
