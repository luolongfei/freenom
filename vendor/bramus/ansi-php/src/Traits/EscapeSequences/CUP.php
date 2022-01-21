<?php

namespace Bramus\Ansi\Traits\EscapeSequences;

/**
 * Trait containing the CUP Escape Function Shorthands
 */
trait CUP
{
    /**
     * Manually use CUP (Cursor position)
     * @param  array $data Parameter byte to the CUP Escape Code
     * @return Ansi  self, for chaining
     */
    public function cup($data = '1;1')
    {
        // Write data to the writer
        $this->writer->write(
            new \Bramus\Ansi\ControlSequences\EscapeSequences\CUP($data)
        );

        // Afford chaining
        return $this;
    }

    /**
     * Move the cursor to coordinates n, m
     * @param  integer  $n the row to move the cursor to
     * @param  integer  $n the column to move the cursor to
     * @return Ansi    self, for chaining
     */
    public function cursorPosition($n = 1, $m = 1)
    {
        return $this->cup("$n;$m");
    }
}
