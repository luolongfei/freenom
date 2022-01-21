<?php

namespace Bramus\Ansi\Traits\EscapeSequences;

/**
 * Trait containing the CUF Escape Function Shorthands
 */
trait CUF
{
    /**
     * Manually use CUF (Move cursor forward)
     * @param  array $data Parameter byte to the CUF Escape Code
     * @return Ansi  self, for chaining
     */
    public function cuf($data = 1)
    {
        // Write data to the writer
        $this->writer->write(
            new \Bramus\Ansi\ControlSequences\EscapeSequences\CUF($data)
        );

        // Afford chaining
        return $this;
    }

    /**
     * Move the cursor n positions forward
     * @param  integer  $n the number of positions to move the cursor
     * @return Ansi     self, for chaining
     */
    public function cursorForward($n = 1)
    {
        return $this->cuf($n);
    }
}
