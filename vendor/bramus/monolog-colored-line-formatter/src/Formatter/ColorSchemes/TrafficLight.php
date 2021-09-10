<?php

namespace Bramus\Monolog\Formatter\ColorSchemes;

use Monolog\Logger;
use Bramus\Ansi\Ansi;
use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;

class TrafficLight implements ColorSchemeInterface
{
    /**
     * Use the ColorScheme and alias its constructor
     */
    use ColorSchemeTrait {
        ColorSchemeTrait::__construct as private __constructTrait;
    }

    /**
     * [__construct description]
     */
    public function __construct()
    {
        // Call Trait Constructor, so that we have $this->ansi available
        $this->__constructTrait();

        // Our Color Scheme
        $this->setColorizeArray(array(
            Logger::DEBUG => $this->ansi->sgr(array(SGR::COLOR_FG_GREEN, SGR::STYLE_INTENSITY_FAINT))->get(),
            Logger::INFO => $this->ansi->sgr(array(SGR::COLOR_FG_GREEN, SGR::STYLE_INTENSITY_NORMAL))->get(),
            Logger::NOTICE => $this->ansi->sgr(array(SGR::COLOR_FG_GREEN, SGR::STYLE_INTENSITY_BRIGHT))->get(),
            Logger::WARNING => $this->ansi->sgr(array(SGR::COLOR_FG_YELLOW, SGR::STYLE_INTENSITY_FAINT))->get(),
            Logger::ERROR => $this->ansi->sgr(array(SGR::COLOR_FG_YELLOW, SGR::STYLE_INTENSITY_NORMAL))->get(),
            Logger::CRITICAL => $this->ansi->sgr(array(SGR::COLOR_FG_RED, SGR::STYLE_INTENSITY_NORMAL))->get(),
            Logger::ALERT => $this->ansi->sgr(array(SGR::COLOR_FG_RED_BRIGHT, SGR::STYLE_INTENSITY_BRIGHT))->get(),
            Logger::EMERGENCY => $this->ansi->sgr(array(SGR::COLOR_FG_RED_BRIGHT, SGR::STYLE_INTENSITY_BRIGHT, SGR::STYLE_BLINK))->get(),
        ));
    }
}
