<?php

use \Bramus\Ansi\Ansi;
use \Bramus\Ansi\Writers\StreamWriter;
use \Bramus\Ansi\ControlFunctions\Enums\C0;
use \Bramus\Ansi\ControlSequences\EscapeSequences\EL as EscapeSequenceEL;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\FinalByte;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\EL;

class EscapeSequenceELTest extends PHPUnit_Framework_TestCase
{

    public function testInstantiation()
    {
        $es = new EscapeSequenceEL(EL::ALL);

        $this->assertInstanceOf('\Bramus\Ansi\ControlSequences\EscapeSequences\EL', $es);

        // Final byte must be EL
        $this->assertEquals(
            $es->getFinalByte(),
            FinalByte::EL
        );

        // Parameter Byte must be EL::ALL
        $this->assertEquals(
            $es->getParameterBytes(),
            EL::ALL
        );
    }

    public function testELRaw()
    {
        // EL::ALL
        $this->assertEquals(
            new EscapeSequenceEL(EL::ALL),
            C0::ESC.'['.EL::ALL.FinalByte::EL
        );

        // EL::TO_EOL
        $this->assertEquals(
            new EscapeSequenceEL(EL::TO_EOL),
            C0::ESC.'['.EL::TO_EOL.FinalByte::EL
        );

        // EL::TO_SOL
        $this->assertEquals(
            new EscapeSequenceEL(EL::TO_SOL),
            C0::ESC.'['.EL::TO_SOL.FinalByte::EL
        );
    }

    public function testAnsiELShorthandsSingle()
    {
        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());

        $this->assertEquals(
            $a->eraseLine()->get(),
            new EscapeSequenceEL(EL::ALL)
        );

        $this->assertEquals(
            $a->eraseLineToEol()->get(),
            new EscapeSequenceEL(EL::TO_EOL)
        );

        $this->assertEquals(
            $a->eraseLineToSol()->get(),
            new EscapeSequenceEL(EL::TO_SOL)
        );
    }

    public function testAnsiELShorthandChained()
    {
        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());
        $es = new EscapeSequenceEL(EL::ALL);

        $this->assertEquals(
            $a->el(EL::ALL)->text('test')->get(),
            $es.'test'
        );
    }

    public function testAnsiELShorthandsChained()
    {

        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());

        $this->assertEquals(
            $a->eraseLine()->eraseLineToEol()->eraseLineToSol()->text('test')->get(),
            (new EscapeSequenceEL(EL::ALL)).(new EscapeSequenceEL(EL::TO_EOL)).(new EscapeSequenceEL(EL::TO_SOL)).'test'
        );
    }
}

// EOF
