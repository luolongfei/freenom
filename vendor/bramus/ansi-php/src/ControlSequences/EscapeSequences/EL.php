<?php
/**
 * EL - ERASE IN LINE
 *
 * EL causes some or all character positions of the active line (the
 * line which contains the active data position in the data component)
 * to be put into the erased state, depending on the parameter values
 *
 */
namespace Bramus\Ansi\ControlSequences\EscapeSequences;

class EL extends Base
{
    // This EscapeSequence has ParameterByte(s)
    use \Bramus\Ansi\ControlSequences\Traits\HasParameterBytes;

    /**
     * EL - ERASE IN LINE
     * @param mixed   $parameterBytes The Parameter Bytes
     */
    public function __construct($parameterBytes)
    {
        // Store the parameter bytes
        $this->setParameterBytes($parameterBytes);

        // Call Parent Constructor (which will store finalByte)
        parent::__construct(
            \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\FinalByte::EL
        );
    }
}
