<?php

use \Bramus\Ansi\Ansi;
use \Bramus\Ansi\Writers\StreamWriter;
use \Bramus\Ansi\ControlFunctions\Enums\C0;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Base as EscapeSequence;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\FinalByte;

class EscapeSequenceTest extends PHPUnit_Framework_TestCase
{

    public function testInstantiation()
    {
        $es = new EscapeSequence(FinalByte::ED);

        $this->assertInstanceOf('\Bramus\Ansi\ControlSequences\EscapeSequences\Base', $es);

        // EscapeSequences MUST start with an Escape Control Function
        $this->assertInstanceOf('\Bramus\Ansi\ControlFunctions\Escape', $es->getControlSequenceIntroducer());

        // The finalByte passed in was ED
        $this->assertEquals(
            $es->getFinalByte(),
            FinalByte::ED
        );
    }

    public function testFinalByte()
    {
        $es = new EscapeSequence(FinalByte::ED);

        // Set new final byte
        $es->setFinalByte(FinalByte::EL);

        // Check for new Final Byte
        $this->assertEquals(
            $es->getFinalByte(),
            FinalByte::EL
        );

        $this->assertEquals(
            $es->get(),
            C0::ESC . '[' . FinalByte::EL
        );

    }

}

// EOF
