<?php

use \Bramus\Ansi\Ansi;
use \Bramus\Ansi\Writers\StreamWriter;
use \Bramus\Ansi\ControlFunctions\Enums\C0;
use \Bramus\Ansi\ControlSequences\EscapeSequences\CUB as EscapeSequenceCUB;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\FinalByte;

class EscapeSequenceCUBTest extends PHPUnit_Framework_TestCase
{

    public function testInstantiation()
    {
        $es = new EscapeSequenceCUB();

        $this->assertInstanceOf('\Bramus\Ansi\ControlSequences\EscapeSequences\CUB', $es);

        // Final byte must be CUB
        $this->assertEquals(
            $es->getFinalByte(),
            FinalByte::CUB
        );

        // Parameter Byte must be the default 1
        $this->assertEquals(
            $es->getParameterBytes(),
            1
        );
    }

    public function testInstantiationWithNonDefaultParameterByte()
    {
        $es = new EscapeSequenceCUB(5);

        $this->assertEquals(
            $es->getParameterBytes(),
            5
        );
    }

    public function testCUBRaw()
    {
        $this->assertEquals(
            new EscapeSequenceCUB(1),
            C0::ESC.'['.'1'.FinalByte::CUB
        );
    }

    public function testAnsiCUBShorthandsSingle()
    {
        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());

        $this->assertEquals(
            $a->cursorBack()->get(),
            new EscapeSequenceCUB()
        );
    }

    public function testAnsiCUBShorthandChained()
    {
        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());
        $es = new EscapeSequenceCUB();

        $this->assertEquals(
            $a->cursorBack()->text('test')->get(),
            $es.'test'
        );
    }

    public function testAnsiCUBShorthandsChained()
    {

        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());

        $this->assertEquals(
            $a->cursorBack()->text('test')->cursorBack()->get(),
            (new EscapeSequenceCUB()).'test'.(new EscapeSequenceCUB())
        );
    }

    public function testAnsiCUBPractical()
    {
        ob_start();

        $numChars = 2;

        $a = new Ansi(new StreamWriter('php://output'));
        $a->text('test')->cursorBack($numChars)->text('overwritten');

        $output = ob_get_contents();
        
        // Split on the escape sequence, and manually trim the first part (because we move back)
        $output = explode(C0::ESC.'['.$numChars.FinalByte::CUB, $output);
        $output[0] = substr($output[0], 0, -$numChars);

        $this->assertEquals('teoverwritten', implode('', $output));

        ob_end_clean();
    }
}

// EOF
