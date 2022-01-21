<?php

use \Bramus\Ansi\Ansi;
use \Bramus\Ansi\Writers\BufferWriter;
use \Bramus\Ansi\ControlFunctions\Enums\C0;

/**
 * Test the Control Functions
 */
class ControlFunctionsTest extends PHPUnit_Framework_TestCase
{

    public function testUsingGet()
    {
        // Base
        $this->assertEquals((new \Bramus\Ansi\ControlFunctions\Base(C0::BS))->get(), C0::BS);

        // Helper Classes
        $this->assertEquals((new \Bramus\Ansi\ControlFunctions\Backspace())->get(), C0::BS);
        $this->assertEquals((new \Bramus\Ansi\ControlFunctions\Backspace())->get(), C0::BACKSPACE);
        $this->assertEquals((new \Bramus\Ansi\ControlFunctions\Bell())->get(), C0::BEL);
        $this->assertEquals((new \Bramus\Ansi\ControlFunctions\Bell())->get(), C0::BELL);
        $this->assertEquals((new \Bramus\Ansi\ControlFunctions\CarriageReturn())->get(), C0::CR);
        $this->assertEquals((new \Bramus\Ansi\ControlFunctions\Escape())->get(), C0::ESC);
        $this->assertEquals((new \Bramus\Ansi\ControlFunctions\LineFeed())->get(), C0::LF);
        $this->assertEquals((new \Bramus\Ansi\ControlFunctions\Tab())->get(), C0::TAB);
    }

    public function testUsingToString()
    {
        // Base
        $this->assertEquals(new \Bramus\Ansi\ControlFunctions\Base(C0::BS), C0::BS);

        // Helper Classes
        $this->assertEquals(new \Bramus\Ansi\ControlFunctions\Backspace(), C0::BS);
        $this->assertEquals(new \Bramus\Ansi\ControlFunctions\Backspace(), C0::BACKSPACE);
        $this->assertEquals(new \Bramus\Ansi\ControlFunctions\Bell(), C0::BEL);
        $this->assertEquals(new \Bramus\Ansi\ControlFunctions\Bell(), C0::BELL);
        $this->assertEquals(new \Bramus\Ansi\ControlFunctions\CarriageReturn(), C0::CR);
        $this->assertEquals(new \Bramus\Ansi\ControlFunctions\Escape(), C0::ESC);
        $this->assertEquals(new \Bramus\Ansi\ControlFunctions\LineFeed(), C0::LF);
        $this->assertEquals(new \Bramus\Ansi\ControlFunctions\Tab(), C0::TAB);
    }

    public function testAnsiShorthands()
    {
        $a = new Ansi(new BufferWriter());

        $this->assertEquals($a->backspace()->flush(), C0::BS);
        $this->assertEquals($a->backspace()->flush(), C0::BACKSPACE);
        $this->assertEquals($a->bell()->flush(), C0::BEL);
        $this->assertEquals($a->bell()->flush(), C0::BELL);
        $this->assertEquals($a->cr()->flush(), C0::CR);
        $this->assertEquals($a->lf()->flush(), C0::LF);
        $this->assertEquals($a->tab()->flush(), C0::TAB);
    }

    public function testAnsiShorthandsChaining()
    {
        $a = new Ansi(new BufferWriter());

        // @note: we are going a round trip (bell is tested twice)
        // to make sure the test before it is also working correctly
        $this->assertEquals(
            $a->bell()->backspace()->cr()->lf()->tab()->bell()->get(),
            C0::BELL.C0::BS.C0::CR.C0::LF.C0::TAB.C0::BEL
        );
    }
}

// EOF
