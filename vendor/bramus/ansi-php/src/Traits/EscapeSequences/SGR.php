<?php

namespace Bramus\Ansi\Traits\EscapeSequences;

use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR as EnumSGR;
use Bramus\Ansi\Ansi;

/**
 * Trait containing the SGR Escape Function Shorthands
 */
trait SGR
{
    /**
     * Manually use SGR (Select Graphic Rendition)
     * @param  array $data Parameter byte to the SGR Escape Code
     * @return Ansi  self, for chaining
     */
    public function sgr($data = array())
    {
        // Write data to the writer
        $this->writer->write(
            new \Bramus\Ansi\ControlSequences\EscapeSequences\SGR($data)
        );

        // Afford chaining
        return $this;
    }

    /**
     * Shorthand to remove all text styling (colors, bold, etc)
     * @return Ansi self, for chaining
     */
    public function nostyle()
    {
        return $this->sgr(array(EnumSGR::STYLE_NONE));
    }

    /**
     * Shorthand to remove all text styling (colors, bold, etc)
     * @return Ansi self, for chaining
     */
    public function reset()
    {
        return $this->nostyle();
    }

    /**
     * Shorthand to set the color.
     * @param  array $color The color you want to set. Use an array filled with ControlSequences\EscapeSequences\Enums\SGR::COLOR_* constants
     * @return Ansi  self, for chaining
     */
    public function color($color = array())
    {
        return $this->sgr($color);
    }

    /**
     * Shorthand to set make text styling to bold (on some systems bright intensity)
     * @return Ansi self, for chaining
     */
    public function bold()
    {
        return $this->sgr(array(EnumSGR::STYLE_BOLD));
    }

    /**
     * Shorthand to set the text intensity to bright (on some systems bold)
     * @return Ansi self, for chaining
     */
    public function bright()
    {
        return $this->sgr(array(EnumSGR::STYLE_INTENSITY_BRIGHT));
    }

    /**
     * Shorthand to set the text styling to normal (no bold/bright)
     * @return Ansi self, for chaining
     */
    public function normal()
    {
        return $this->sgr(array(EnumSGR::STYLE_INTENSITY_NORMAL));
    }

    /**
     * (Not widely supported) Shorthand to set the text intensity to faint
     * @return Ansi self, for chaining
     */
    public function faint()
    {
        return $this->sgr(array(EnumSGR::STYLE_INTENSITY_FAINT));
    }

    /**
     * (Not widely supported) Shorthand to set the text styling to italic
     * @return Ansi self, for chaining
     */
    public function italic()
    {
        return $this->sgr(array(EnumSGR::STYLE_ITALIC));
    }

    /**
     * Shorthand to set the text styling to underline
     * @return Ansi self, for chaining
     */
    public function underline()
    {
        return $this->sgr(array(EnumSGR::STYLE_UNDERLINE));
    }

    /**
     * Shorthand to set the text styling to blink
     * @return Ansi self, for chaining
     */
    public function blink()
    {
        return $this->sgr(array(EnumSGR::STYLE_BLINK));
    }

    /**
     * Shorthand to set the text styling to reserved (viz. swap background & foreground color)
     * @return Ansi self, for chaining
     */
    public function negative()
    {
        return $this->sgr(array(EnumSGR::STYLE_NEGATIVE));
    }

    /**
     * (Not widely supported) Shorthand to set the text styling to strikethrough
     * @return Ansi self, for chaining
     */
    public function strikethrough()
    {
        return $this->sgr(array(EnumSGR::STYLE_STRIKETHROUGH));
    }
}
