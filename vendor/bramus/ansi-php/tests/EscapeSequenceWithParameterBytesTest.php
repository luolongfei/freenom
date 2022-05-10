<?php

use \Bramus\Ansi\ControlFunctions\Enums\C0;
use \Bramus\Ansi\ControlSequences\EscapeSequences\SGR as EscapeSequenceSGR;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\FinalByte;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;

class EscapeSequenceWithParameterBytesTest extends PHPUnit_Framework_TestCase
{
    public function testParameterBytesSingle()
    {
        $es = new EscapeSequenceSGR(0);

        $this->assertEquals(
            $es->getParameterBytes(),
            0
        );

        $this->assertEquals(
            $es->get(),
            C0::ESC . '[' . 0 . FinalByte::SGR
        );
    }

    public function testParameterBytesSingleAdd()
    {
        $es = new EscapeSequenceSGR(0);

        // Add a parameter byte
        $es->addParameterByte(1);

        $this->assertEquals(
            $es->getParameterBytes(),
            '01'
        );

        $this->assertEquals(
            $es->get(),
            C0::ESC . '[' . '0;1' . FinalByte::SGR
        );

    }

    public function testParameterBytesArray()
    {
        $es = new EscapeSequenceSGR(array(0, 1));

        // Check for new Final Byte
        $this->assertEquals(
            $es->getParameterBytes(),
            '01'
        );

        $this->assertEquals(
            $es->get(),
            C0::ESC . '[' . '0;1' . FinalByte::SGR
        );

    }
}
