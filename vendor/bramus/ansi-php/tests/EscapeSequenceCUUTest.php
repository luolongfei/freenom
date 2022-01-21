<?php

use \Bramus\Ansi\Ansi;
use \Bramus\Ansi\Writers\StreamWriter;
use \Bramus\Ansi\ControlFunctions\Enums\C0;
use \Bramus\Ansi\ControlSequences\EscapeSequences\CUU as EscapeSequenceCUU;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\FinalByte;

class EscapeSequenceCUUTest extends PHPUnit_Framework_TestCase
{

    public function testInstantiation()
    {
        $es = new EscapeSequenceCUU();

        $this->assertInstanceOf('\Bramus\Ansi\ControlSequences\EscapeSequences\CUU', $es);

        // Final byte must be CUU
        $this->assertEquals(
            $es->getFinalByte(),
            FinalByte::CUU
        );

        // Parameter Byte must be the default 1
        $this->assertEquals(
            $es->getParameterBytes(),
            1
        );
    }

    public function testInstantiationWithNonDefaultParameterByte()
    {
        $es = new EscapeSequenceCUU(5);

        $this->assertEquals(
            $es->getParameterBytes(),
            5
        );
    }

    public function testCUURaw()
    {
        $this->assertEquals(
            new EscapeSequenceCUU(1),
            C0::ESC.'['.'1'.FinalByte::CUU
        );
    }

    public function testAnsiCUUShorthandsSingle()
    {
        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());

        $this->assertEquals(
            $a->cursorUp()->get(),
            new EscapeSequenceCUU()
        );
    }

    public function testAnsiCUUShorthandChained()
    {
        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());
        $es = new EscapeSequenceCUU();

        $this->assertEquals(
            $a->cursorUp()->text('test')->get(),
            $es.'test'
        );
    }

    public function testAnsiCUUShorthandsChained()
    {

        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());

        $this->assertEquals(
            $a->cursorUp()->text('test')->cursorUp()->get(),
            (new EscapeSequenceCUU()).'test'.(new EscapeSequenceCUU())
        );
    }
}

// EOF
