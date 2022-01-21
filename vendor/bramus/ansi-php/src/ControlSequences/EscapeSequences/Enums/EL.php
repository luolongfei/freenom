<?php
/**
 * Possible Parameter Byte Values for EL
 */
namespace Bramus\Ansi\ControlSequences\EscapeSequences\Enums;

class EL
{
    /**
     * Erases from the current cursor position to the end of the current line.
     * @type string
     */
    const TO_EOL = '0';

    /**
     * Erases from the current cursor position to the start of the current line.
     * @type string
     */
    const TO_SOL = '1';

    /**
     * Erases the entire current line.
     * @type string
     */
    const ALL = '2';
}
