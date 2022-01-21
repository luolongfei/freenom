<?php

namespace Bramus\Ansi\Traits\EscapeSequences;

use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\EL as EnumEL;
use Bramus\Ansi\Ansi;

/**
 * Trait containing the EL Escape Function Shorthands
 */
trait EL
{
    /**
     * Manually use EL (ERASE IN LINE)
     * @param  array $data Parameter byte to the EL Escape Code
     * @return Ansi  self, for chaining
     */
    public function el($data)
    {
        // Write data to the writer
        $this->writer->write(
            new \Bramus\Ansi\ControlSequences\EscapeSequences\EL($data)
        );

        // Afford chaining
        return $this;
    }

    /**
     * Erase from the current cursor position to the end of the current line.
     * @param  boolean $outputNow Echo the character right now, or add it to the buffer building?
     * @return Ansi    self, for chaining
     */
    public function eraseLineToEOL()
    {
        return $this->el(EnumEL::TO_EOL);
    }

    /**
     * Erases from the current cursor position to the start of the current line.
     * @param  boolean $outputNow Echo the character right now, or add it to the buffer building?
     * @return Ansi    self, for chaining
     */
    public function eraseLineToSOL()
    {
        return $this->el(EnumEL::TO_SOL);
    }

    /**
     * Erase the entire current line.
     * @param  boolean $outputNow Echo the character right now, or add it to the buffer building?
     * @return Ansi    self, for chaining
     */
    public function eraseLine()
    {
        return $this->el(EnumEL::ALL);
    }
}
