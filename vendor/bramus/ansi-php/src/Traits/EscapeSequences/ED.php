<?php

namespace Bramus\Ansi\Traits\EscapeSequences;

use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\ED as EnumED;
use Bramus\Ansi\Ansi;

/**
 * Trait containing the ED Escape Function Shorthands
 */
trait ED
{
    /**
     * Manually use ED (Select Graphic Rendition)
     * @param  array $data Parameter byte to the SGR Escape Code
     * @return Ansi  self, for chaining
     */
    public function ed($data)
    {
        // Write data to the writer
        $this->writer->write(
            new \Bramus\Ansi\ControlSequences\EscapeSequences\ED($data)
        );

        // Afford chaining
        return $this;
    }

    /**
     * Erase the screen from the current line up to the top of the screen
     * @return Ansi self, for chaining
     */
    public function eraseDisplayUp()
    {
        return $this->ed(EnumED::UP);
    }

    /**
     * Erase the screen from the current line down to the bottom of the screen
     * @return Ansi self, for chaining
     */
    public function eraseDisplayDown()
    {
        return $this->ed(EnumED::DOWN);
    }

    /**
     * Erase the entire screen and moves the cursor to home
     * @return Ansi self, for chaining
     */
    public function eraseDisplay()
    {
        return $this->ed(EnumED::ALL);
    }
}
