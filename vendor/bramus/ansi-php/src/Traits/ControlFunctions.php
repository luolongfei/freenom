<?php

namespace Bramus\Ansi\Traits;

/**
 * Trait containing the Control Function Shorthands
 */
trait ControlFunctions
{
    /**
     * Add a Bell Control Character to the buffer / echo it on screen
     * @return Ansi self, for chaining
     */
    public function bell()
    {
        // Write character onto writer
        $this->writer->write(new \Bramus\Ansi\ControlFunctions\Bell());

        // Afford chaining
        return $this;
    }

    /**
     * Add a Backspace Control Character to the buffer / echo it on screen
     * @return Ansi self, for chaining
     */
    public function backspace()
    {
        // Write character onto writer
        $this->writer->write(new \Bramus\Ansi\ControlFunctions\Backspace());

        // Afford chaining
        return $this;
    }

    /**
     * Add a Tab Control Character to the buffer / echo it on screen
     * @return Ansi self, for chaining
     */
    public function tab()
    {
        // Write character onto writer
        $this->writer->write(new \Bramus\Ansi\ControlFunctions\Tab());

        // Afford chaining
        return $this;
    }

    /**
     * Add a Line Feed Control Character to the buffer / echo it on screen
     * @return Ansi self, for chaining
     */
    public function lf()
    {
        // Write character onto writer
        $this->writer->write(new \Bramus\Ansi\ControlFunctions\LineFeed());

        // Afford chaining
        return $this;
    }

    /**
     * Add a Carriage Return Control Character to the buffer / echo it on screen
     * @return Ansi self, for chaining
     */
    public function cr()
    {
        // Write character onto writer
        $this->writer->write(new \Bramus\Ansi\ControlFunctions\CarriageReturn());

        // Afford chaining
        return $this;
    }
}
