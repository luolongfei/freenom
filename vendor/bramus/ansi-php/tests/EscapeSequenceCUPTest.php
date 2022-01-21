<?php

use \Bramus\Ansi\Ansi;
use \Bramus\Ansi\Writers\StreamWriter;
use \Bramus\Ansi\ControlFunctions\Enums\C0;
use \Bramus\Ansi\ControlSequences\EscapeSequences\CUP as EscapeSequenceCUP;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\FinalByte;

class EscapeSequenceCUPTest extends PHPUnit_Framework_TestCase
{

    public function testInstantiation()
    {
        $es = new EscapeSequenceCUP();

        $this->assertInstanceOf('\Bramus\Ansi\ControlSequences\EscapeSequences\CUP', $es);

        // Final byte must be CUP
        $this->assertEquals(
            $es->getFinalByte(),
            FinalByte::CUP
        );

        // Parameter Byte must be the default 1, 1
        $this->assertEquals(
            $es->getParameterBytes(),
            '1;1'
        );
    }

    public function testInstantiationWithNonDefaultParameterByte()
    {
        $es = new EscapeSequenceCUP('5;6');

        $this->assertEquals(
            $es->getParameterBytes(),
            '5;6'
        );
    }

    public function testCUPRaw()
    {
        $this->assertEquals(
            new EscapeSequenceCUP('1;1'),
            C0::ESC.'['.'1;1'.FinalByte::CUP
        );
    }

    public function testAnsiCUPShorthandsSingle()
    {
        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());

        $this->assertEquals(
            $a->cursorPosition()->get(),
            new EscapeSequenceCUP()
        );
    }

    public function testAnsiCUPShorthandChained()
    {
        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());
        $es = new EscapeSequenceCUP();

        $this->assertEquals(
            $a->cursorPosition()->text('test')->get(),
            $es.'test'
        );
    }

    public function testAnsiCUPShorthandsChained()
    {

        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());

        $this->assertEquals(
            $a->cursorPosition()->text('test')->cursorPosition()->get(),
            (new EscapeSequenceCUP()).'test'.(new EscapeSequenceCUP())
        );
    }
}

// EOF
