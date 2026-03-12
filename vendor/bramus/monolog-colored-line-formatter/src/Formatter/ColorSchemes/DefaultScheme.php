<?php

namespace Bramus\Monolog\Formatter\ColorSchemes;

use Monolog\Level;
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
            Level::Debug->value => $this->ansi->color([SGR::COLOR_FG_WHITE])->get(),
            Level::Info->value => $this->ansi->color([SGR::COLOR_FG_GREEN])->get(),
            Level::Notice->value => $this->ansi->color([SGR::COLOR_FG_CYAN])->get(),
            Level::Warning->value => $this->ansi->color([SGR::COLOR_FG_YELLOW])->get(),
            Level::Error->value => $this->ansi->color([SGR::COLOR_FG_RED])->get(),
            Level::Critical->value => $this->ansi->color([SGR::COLOR_FG_RED])->underline()->get(),
            Level::Alert->value => $this->ansi->color([SGR::COLOR_FG_WHITE, SGR::COLOR_BG_RED_BRIGHT])->get(),
            Level::Emergency->value => $this->ansi->color([SGR::COLOR_BG_RED_BRIGHT])->blink()->color([SGR::COLOR_FG_WHITE])->get(),
        ));
    }
}
