<?php

use \Bramus\Ansi\Ansi;
use \Bramus\Ansi\Writers\StreamWriter;
use \Bramus\Ansi\ControlFunctions\Enums\C0;
use \Bramus\Ansi\ControlSequences\EscapeSequences\CUF as EscapeSequenceCUF;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\FinalByte;

class EscapeSequenceCUFTest extends PHPUnit_Framework_TestCase
{

    public function testInstantiation()
    {
        $es = new EscapeSequenceCUF();

        $this->assertInstanceOf('\Bramus\Ansi\ControlSequences\EscapeSequences\CUF', $es);

        // Final byte must be CUF
        $this->assertEquals(
            $es->getFinalByte(),
            FinalByte::CUF
        );

        // Parameter Byte must be the default 1
        $this->assertEquals(
            $es->getParameterBytes(),
            1
        );
    }

    public function testInstantiationWithNonDefaultParameterByte()
    {
        $es = new EscapeSequenceCUF(5);

        $this->assertEquals(
            $es->getParameterBytes(),
            5
        );
    }

    public function testCUFRaw()
    {
        $this->assertEquals(
            new EscapeSequenceCUF(1),
            C0::ESC.'['.'1'.FinalByte::CUF
        );
    }

    public function testAnsiCUFShorthandsSingle()
    {
        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());

        $this->assertEquals(
            $a->cursorForward()->get(),
            new EscapeSequenceCUF()
        );
    }

    public function testAnsiCUFShorthandChained()
    {
        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());
        $es = new EscapeSequenceCUF();

        $this->assertEquals(
            $a->cursorForward()->text('test')->get(),
            $es.'test'
        );
    }

    public function testAnsiCUFShorthandsChained()
    {

        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());

        $this->assertEquals(
            $a->cursorForward()->text('test')->cursorForward()->get(),
            (new EscapeSequenceCUF()).'test'.(new EscapeSequenceCUF())
        );
    }
}

// EOF
