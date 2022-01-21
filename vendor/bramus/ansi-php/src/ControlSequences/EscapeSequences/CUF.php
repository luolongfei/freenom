<?php
/**
 * CUF - CURSOR FORWARD
 * 
 * CUF causes the active presentation position to be moved rightwards
 * in the presentation component by n character positions if the
 * character path is horizontal, or by n line positions if the
 * character path is vertical, where n equals the value of Pn.
 */
namespace Bramus\Ansi\ControlSequences\EscapeSequences;

class CUF extends Base
{
    // This EscapeSequence has ParameterByte(s)
    use \Bramus\Ansi\ControlSequences\Traits\HasParameterBytes;

    /**
     * CUF - CURSOR FORWARD
     * @param mixed   $parameterBytes The Parameter Bytes
     */
    public function __construct($parameterBytes = 1)
    {
        // Store the parameter bytes
        $this->setParameterBytes($parameterBytes);

        // Call Parent Constructor (which will store finalByte)
        parent::__construct(
            \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\FinalByte::CUF
        );
    }
}
