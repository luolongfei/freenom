<?php

use \Bramus\Ansi\Ansi;
use \Bramus\Ansi\Writers\StreamWriter;
use \Bramus\Ansi\Writers\BufferWriter;
use \Bramus\Ansi\ControlFunctions\Enums\C0;

/**
 * Test the Ansi Class and its core functions
 */
class AnsiTest extends PHPUnit_Framework_TestCase
{

    public function testInstantiation()
    {

        // Create Ansi Instance (using default writer)
        $a = new Ansi();
        $this->assertInstanceOf('\Bramus\Ansi\Ansi', $a);
        $this->assertInstanceOf('\Bramus\Ansi\Writers\StreamWriter', $a->getWriter());

        // Create Ansi Instance (using custom writer)
        $a = new Ansi(new BufferWriter());
        $this->assertInstanceOf('\Bramus\Ansi\Ansi', $a);
        $this->assertInstanceOf('\Bramus\Ansi\Writers\BufferWriter', $a->getWriter());

    }

    public function testFunctions()
    {

    }

    public function testChaining()
    {
        $a = new Ansi(new BufferWriter());
        $test = $a->text('foo')->text('bar')->get();

        $this->assertEquals(
            $test,
            'foobar'
        );
    }

}