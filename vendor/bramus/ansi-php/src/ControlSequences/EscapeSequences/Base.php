<?php
/**
 * ANSI Escape Sequence
 *
 * A string of bit combinations that is used for control purposes in
 * code extension procedures. The first of these bit combinations
 * represents the control function ESCAPE.
 */
namespace Bramus\Ansi\ControlSequences\EscapeSequences;

class Base extends \Bramus\Ansi\ControlSequences\Base
{
    // Escape Sequences have a final byte
    use \Bramus\Ansi\ControlSequences\Traits\HasFinalByte;

    /**
     * ANSI Escape Sequence
     * @param string  $finalByte The Final Byte of the Escape Sequence
     */
    public function __construct($finalByte)
    {
        // Store the final byte
        $this->setFinalByte($finalByte);

        // Call Parent Constructor
        parent::__construct(
            new \Bramus\Ansi\ControlFunctions\Escape()
        );
    }
}
