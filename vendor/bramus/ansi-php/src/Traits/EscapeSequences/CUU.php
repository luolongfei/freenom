<?php

namespace Bramus\Ansi\Traits\EscapeSequences;

/**
 * Trait containing the CUU Escape Function Shorthands
 */
trait CUU
{
    /**
     * Manually use CUU (Move cursor up)
     * @param  array $data Parameter byte to the CUU Escape Code
     * @return Ansi  self, for chaining
     */
    public function cuu($data = 1)
    {
        // Write data to the writer
        $this->writer->write(
            new \Bramus\Ansi\ControlSequences\EscapeSequences\CUU($data)
        );

        // Afford chaining
        return $this;
    }

    /**
     * Move the cursor up n positions
     * @param  integer  $n the number of positions to move the cursor
     * @return Ansi    self, for chaining
     */
    public function cursorUp($n = 1)
    {
        return $this->cuu($n);
    }
}
