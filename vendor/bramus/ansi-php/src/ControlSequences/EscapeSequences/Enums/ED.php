<?php
/**
 * Possible Parameter Byte Values for ED
 */
namespace Bramus\Ansi\ControlSequences\EscapeSequences\Enums;

class ED
{
    /**
     * Erases the screen from the current line down to the bottom of the screen.
     * @type string
     */
    const DOWN = '0';

    /**
     * Erases the screen from the current line up to the top of the screen.
     * @type string
     */
    const UP = '1';

    /**
     * Erases the screen with the background colour and moves the cursor to home.
     * @type string
     */
    const ALL = '2';
}
