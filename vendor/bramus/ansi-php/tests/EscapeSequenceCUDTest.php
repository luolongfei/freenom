<?php

use \Bramus\Ansi\Ansi;
use \Bramus\Ansi\Writers\StreamWriter;
use \Bramus\Ansi\ControlFunctions\Enums\C0;
use \Bramus\Ansi\ControlSequences\EscapeSequences\CUD as EscapeSequenceCUD;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\FinalByte;

class EscapeSequenceCUDTest extends PHPUnit_Framework_TestCase
{

    public function testInstantiation()
    {
        $es = new EscapeSequenceCUD();

        $this->assertInstanceOf('\Bramus\Ansi\ControlSequences\EscapeSequences\CUD', $es);

        // Final byte must be CUD
        $this->assertEquals(
            $es->getFinalByte(),
            FinalByte::CUD
        );

        // Parameter Byte must be the default 1
        $this->assertEquals(
            $es->getParameterBytes(),
            1
        );
    }

    public function testInstantiationWithNonDefaultParameterByte()
    {
        $es = new EscapeSequenceCUD(5);

        $this->assertEquals(
            $es->getParameterBytes(),
            5
        );
    }

    public function testCUDRaw()
    {
        $this->assertEquals(
            new EscapeSequenceCUD(1),
            C0::ESC.'['.'1'.FinalByte::CUD
        );
    }

    public function testAnsiCUDShorthandsSingle()
    {
        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());

        $this->assertEquals(
            $a->cursorDown()->get(),
            new EscapeSequenceCUD()
        );
    }

    public function testAnsiCUDShorthandChained()
    {
        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());
        $es = new EscapeSequenceCUD();

        $this->assertEquals(
            $a->cursorDown()->text('test')->get(),
            $es.'test'
        );
    }

    public function testAnsiCUDShorthandsChained()
    {

        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());

        $this->assertEquals(
            $a->cursorDown()->text('test')->cursorDown()->get(),
            (new EscapeSequenceCUD()).'test'.(new EscapeSequenceCUD())
        );
    }
}

// EOF
