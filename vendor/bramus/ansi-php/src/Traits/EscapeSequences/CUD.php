<?php

namespace Bramus\Ansi\Traits\EscapeSequences;

/**
 * Trait containing the CUD Escape Function Shorthands
 */
trait CUD
{
    /**
     * Manually use CUD (Move cursor down)
     * @param  array $data Parameter byte to the CUD Escape Code
     * @return Ansi  self, for chaining
     */
    public function cud($data = 1)
    {
        // Write data to the writer
        $this->writer->write(
            new \Bramus\Ansi\ControlSequences\EscapeSequences\CUD($data)
        );

        // Afford chaining
        return $this;
    }

    /**
     * Move the cursor n positions down
     * @param  integer  $n the number of positions to move the cursor
     * @return Ansi     self, for chaining
     */
    public function cursorDown($n = 1)
    {
        return $this->cud($n);
    }
}
