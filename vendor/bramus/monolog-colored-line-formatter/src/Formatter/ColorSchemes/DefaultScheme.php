<?php

namespace Bramus\Monolog\Formatter\ColorSchemes;

use Monolog\Logger;
use Bramus\Ansi\Ansi;
use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;

class DefaultScheme implements ColorSchemeInterface
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
            Logger::DEBUG => $this->ansi->color(SGR::COLOR_FG_WHITE)->get(),
            Logger::INFO => $this->ansi->color(SGR::COLOR_FG_GREEN)->get(),
            Logger::NOTICE => $this->ansi->color(SGR::COLOR_FG_CYAN)->get(),
            Logger::WARNING => $this->ansi->color(SGR::COLOR_FG_YELLOW)->get(),
            Logger::ERROR => $this->ansi->color(SGR::COLOR_FG_RED)->get(),
            Logger::CRITICAL => $this->ansi->color(SGR::COLOR_FG_RED)->underline()->get(),
            Logger::ALERT => $this->ansi->color(array(SGR::COLOR_FG_WHITE, SGR::COLOR_BG_RED_BRIGHT))->get(),
            Logger::EMERGENCY => $this->ansi->color(SGR::COLOR_BG_RED_BRIGHT)->blink()->color(SGR::COLOR_FG_WHITE)->get(),
        ));
    }
}
