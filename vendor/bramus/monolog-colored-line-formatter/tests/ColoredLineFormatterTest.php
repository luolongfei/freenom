<?php

use \Monolog\Logger;
use \Bramus\Monolog\Formatter\ColoredLineFormatter;
use Bramus\Ansi\Ansi;
use Bramus\Ansi\Writers\BufferWriter;
use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;

class ColoredLineFormatterTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->ansi = new Ansi(new BufferWriter());
        $this->clf = new ColoredLineFormatter();
    }

    protected function tearDown()
    {
        // ...
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf('\Bramus\Monolog\Formatter\ColoredLineFormatter', $this->clf);
    }

    public function testReset()
    {
        $this->assertEquals($this->clf->getColorScheme()->getResetString(), "\033[0m");
    }

    public function testDefaultColorScheme()
    {
        $defaultScheme = new \Bramus\Monolog\Formatter\ColorSchemes\DefaultScheme();

        $this->assertEquals(
            $this->clf->getColorScheme()->getColorizeArray(),
            $defaultScheme->getColorizeArray()
        );
    }

    public function testSetColorSchemeViaConstructor()
    {
        $newScheme = new \Bramus\Monolog\Formatter\ColorSchemes\TrafficLight();
        $this->clf = new ColoredLineFormatter($newScheme);

        $this->assertEquals(
            $this->clf->getColorScheme()->getColorizeArray(),
            $newScheme->getColorizeArray()
        );
    }

    public function testSetColorSchemeViaSetColorScheme()
    {
        $newScheme = new \Bramus\Monolog\Formatter\ColorSchemes\TrafficLight();
        $this->clf->setColorScheme($newScheme);

        $this->assertEquals(
            $this->clf->getColorScheme()->getColorizeArray(),
            $newScheme->getColorizeArray()
        );
    }

    public function testSetColorSchemeFilter()
    {

        $dummyArray = array(
            Logger::DEBUG => $this->ansi->sgr(array(SGR::COLOR_FG_GREEN, SGR::STYLE_INTENSITY_FAINT))->get(),
            '123' => 'foo',
            9000 => 'bar',
            Logger::INFO => $this->ansi->sgr(array(SGR::COLOR_FG_GREEN, SGR::STYLE_INTENSITY_NORMAL))->get(),
            Logger::NOTICE => $this->ansi->sgr(array(SGR::COLOR_FG_GREEN, SGR::STYLE_INTENSITY_BRIGHT))->get(),
            'foo' => 200,
            Logger::WARNING => $this->ansi->sgr(array(SGR::COLOR_FG_YELLOW, SGR::STYLE_INTENSITY_FAINT))->get(),
            Logger::ERROR => $this->ansi->sgr(array(SGR::COLOR_FG_YELLOW, SGR::STYLE_INTENSITY_NORMAL))->get(),
            Logger::CRITICAL => $this->ansi->sgr(array(SGR::COLOR_FG_RED, SGR::STYLE_INTENSITY_NORMAL))->get(),
            Logger::ALERT => $this->ansi->sgr(array(SGR::COLOR_FG_RED_BRIGHT, SGR::STYLE_INTENSITY_BRIGHT))->get(),
            Logger::EMERGENCY => $this->ansi->sgr(array(SGR::COLOR_FG_RED_BRIGHT, SGR::STYLE_INTENSITY_BRIGHT, SGR::STYLE_BLINK))->get(),
        );

        $this->clf->getColorScheme()->setColorizeArray($dummyArray);

        foreach(Logger::getLevels() as $level)
        {
            $this->assertArrayHasKey($level, $this->clf->getColorScheme()->getColorizeArray());
        }

        $this->assertArrayNotHasKey('123', $this->clf->getColorScheme()->getColorizeArray());
        $this->assertArrayNotHasKey('foo', $this->clf->getColorScheme()->getColorizeArray());
        $this->assertArrayNotHasKey(9000, $this->clf->getColorScheme()->getColorizeArray());

    }

    // public function testDemo()
    // {
    //     foreach (Logger::getLevels() as $level) {
    //         echo $this->clf->format(array(
    //             'level' => $level,
    //             'level_name' => Logger::getLevelName($level),
    //             'channel' => 'DEMO',
    //             'message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
    //             'datetime' => \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)), new \DateTimeZone(date_default_timezone_get() ?: 'UTC')),
    //             'context' => array(),
    //             'extra' => array(),
    //         ));
    //     }
    // }
}

// EOF
