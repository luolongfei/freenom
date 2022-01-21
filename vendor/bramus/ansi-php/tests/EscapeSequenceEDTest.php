<?php

use \Bramus\Ansi\Ansi;
use \Bramus\Ansi\Writers\StreamWriter;
use \Bramus\Ansi\ControlFunctions\Enums\C0;
use \Bramus\Ansi\ControlSequences\EscapeSequences\ED as EscapeSequenceED;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\FinalByte;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\ED;

class EscapeSequenceEDTest extends PHPUnit_Framework_TestCase
{

    public function testInstantiation()
    {
        $es = new EscapeSequenceED(ED::ALL);

        $this->assertInstanceOf('\Bramus\Ansi\ControlSequences\EscapeSequences\ED', $es);

        // Final byte must be ED
        $this->assertEquals(
            $es->getFinalByte(),
            FinalByte::ED
        );

        // Parameter Byte must be ED::ALL
        $this->assertEquals(
            $es->getParameterBytes(),
            ED::ALL
        );
    }

    public function testEDRaw()
    {
        // ED::ALL
        $this->assertEquals(
            new EscapeSequenceED(ED::ALL),
            C0::ESC.'['.ED::ALL.FinalByte::ED
        );

        // ED::UP
        $this->assertEquals(
            new EscapeSequenceED(ED::UP),
            C0::ESC.'['.ED::UP.FinalByte::ED
        );

        // ED::DOWN
        $this->assertEquals(
            new EscapeSequenceED(ED::DOWN),
            C0::ESC.'['.ED::DOWN.FinalByte::ED
        );
    }

    public function testAnsiEDShorthandsSingle()
    {
        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());

        $this->assertEquals(
            $a->eraseDisplayUp()->get(),
            new EscapeSequenceED(ED::UP)
        );

        $this->assertEquals(
            $a->eraseDisplayDown()->get(),
            new EscapeSequenceED(ED::DOWN)
        );

        $this->assertEquals(
            $a->eraseDisplay()->get(),
            new EscapeSequenceED(ED::ALL)
        );
    }

    public function testAnsiEDShorthandChained()
    {
        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());
        $es = new EscapeSequenceED(ED::ALL);

        $this->assertEquals(
            $a->ed(ED::ALL)->text('test')->get(),
            $es.'test'
        );
    }

    public function testAnsiEDShorthandsChained()
    {

        $a = new Ansi(new \Bramus\Ansi\Writers\BufferWriter());

        $this->assertEquals(
            $a->eraseDisplayUp()->eraseDisplayDown()->eraseDisplay()->text('test')->get(),
            (new EscapeSequenceED(ED::UP)).(new EscapeSequenceED(ED::DOWN)).(new EscapeSequenceED(ED::ALL)).'test'
        );
    }
}

// EOF
