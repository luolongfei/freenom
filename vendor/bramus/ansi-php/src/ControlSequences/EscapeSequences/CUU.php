<?php
/**
 * CUU - CURSOR UP
 *
 * CUU causes the active presentation position to be moved upwards in
 * the presentation component by n line positions if the character
 * path is horizontal, or by n character positions if the character
 * path is vertical, where n equals the value of Pn.
 */
namespace Bramus\Ansi\ControlSequences\EscapeSequences;

class CUU extends Base
{
    // This EscapeSequence has ParameterByte(s)
    use \Bramus\Ansi\ControlSequences\Traits\HasParameterBytes;

    /**
     * CUU - CURSOR UP
     * @param mixed   $parameterBytes The Parameter Bytes
     */
    public function __construct($parameterBytes = 1)
    {
        // Store the parameter bytes
        $this->setParameterBytes($parameterBytes);

        // Call Parent Constructor (which will store finalByte)
        parent::__construct(
            \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\FinalByte::CUU
        );
    }
}
