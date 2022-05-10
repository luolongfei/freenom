<?php
/**
 * CUP - CURSOR POSITION
 * 
 * CUP causes the active presentation position to be moved in the
 * presentation component to the n-th line position according to the
 * line progression and to the m-th character position according to
 * the character path, where n equals the value of Pn1 and m equals
 * the value of Pn2.
 */
namespace Bramus\Ansi\ControlSequences\EscapeSequences;

class CUP extends Base
{
    // This EscapeSequence has ParameterByte(s)
    use \Bramus\Ansi\ControlSequences\Traits\HasParameterBytes;

    /**
     * CUP - CURSOR POSITION
     * @param mixed   $parameterBytes The Parameter Bytes
     */
    public function __construct($parameterBytes = '1;1')
    {
        // Store the parameter bytes
        $this->setParameterBytes($parameterBytes);

        // Call Parent Constructor (which will store finalByte)
        parent::__construct(
            \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\FinalByte::CUP
        );
    }
}
