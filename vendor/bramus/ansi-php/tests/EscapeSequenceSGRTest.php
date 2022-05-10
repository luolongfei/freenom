<?php

use \Bramus\Ansi\Ansi;
use \Bramus\Ansi\Writers\StreamWriter;
use \Bramus\Ansi\ControlFunctions\Enums\C0;
use \Bramus\Ansi\ControlSequences\EscapeSequences\SGR as EscapeSequenceSGR;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\FinalByte;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;

class EscapeSequenceSGRTest extends PHPUnit_Framework_TestCase
{

    public function testInstantiation()
    {
        $es = new EscapeSequenceSGR();

        $this->assertInstanceOf('\Bramus\Ansi\ControlSequences\EscapeSequences\SGR', $es);

        // Final byte must be SGR
        $this->assertEquals(
            $es->getFinalByte(),
            FinalByte::SGR
        );

        // Parameter Byte must be SGR::STYLE_NONE
        $this->assertEquals(
            $es->getParameterBytes(),
            SGR::STYLE_NONE
        );
    }

    public function testSgrRaw()
    {
        // One Parameter
        $this->assertEquals(
            new EscapeSequenceSGR(SGR::STYLE_NONE),
            C0::ESC.'['.SGR::STYLE_NONE.FinalByte::SGR
        );

        // Multiple Parameters
        $this->assertEquals(
            new EscapeSequenceSGR(array(SGR::STYLE_NONE, SGR::STYLE_BOLD)),
            C0::ESC.'['.SGR::STYLE_NONE.';'.SGR::STYLE_BOLD.FinalByte::SGR
        );
    }

    public function testAnsiSgrShorthandsSingle()
    {
        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());

        $this->assertEquals(
            $a->sgr(SGR::COLOR_FG_RED)->get(),
            new EscapeSequenceSGR(SGR::COLOR_FG_RED)
        );

        $this->assertEquals(
            $a->sgr()->get(),
            new EscapeSequenceSGR(SGR::STYLE_NONE)
        );
    }

    public function testAnsiSgrShorthandChained()
    {

        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());

        $this->assertEquals(
            $a->sgr(SGR::COLOR_FG_RED)->text('test')->get(),
            (new EscapeSequenceSGR(SGR::COLOR_FG_RED)).'test'
        );
    }

    public function testAnsiSgrShorthandsChained()
    {

        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());

        $this->assertEquals(
            $a->bold()->underline()->color(SGR::COLOR_FG_RED)->underline()->blink()->text('test')->reset()->get(),
            (new EscapeSequenceSGR(SGR::STYLE_BOLD)).(new EscapeSequenceSGR(SGR::STYLE_UNDERLINE)).(new EscapeSequenceSGR(SGR::COLOR_FG_RED)).(new EscapeSequenceSGR(SGR::STYLE_UNDERLINE)).(new EscapeSequenceSGR(SGR::STYLE_BLINK)).'test'.(new EscapeSequenceSGR(SGR::STYLE_NONE))
        );
    }
}

// EOF
