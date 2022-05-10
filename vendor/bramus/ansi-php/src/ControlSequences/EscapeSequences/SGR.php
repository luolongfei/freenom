<?php
/**
 * SGR - SELECT GRAPHIC RENDITION
 *
 * SGR is used to establish one or more graphic rendition aspects for
 * subsequent text. The established aspects remain in effect until the
 * next occurrence of SGR in the data stream, depending on the setting
 * of the GRAPHIC RENDITION COMBINATION MODE (GRCM). Each graphic
 * rendition aspect is specified by a parameter value
 */
namespace Bramus\Ansi\ControlSequences\EscapeSequences;

class SGR extends Base
{
    // This EscapeSequence has ParameterByte(s)
    use \Bramus\Ansi\ControlSequences\Traits\HasParameterBytes;

    /**
     * SGR - SELECT GRAPHIC RENDITION
     * @param mixed   $parameterBytes The Parameter Bytes
     */
    public function __construct($parameterBytes = null)
    {
        // Make sure we have parameter bytes
        if (!$parameterBytes) {
            $parameterBytes = array(Enums\SGR::STYLE_NONE);
        }

        // Store the parameter bytes
        $this->setParameterBytes($parameterBytes);

        // Call Parent Constructor (which will store finalByte)
        parent::__construct(
            \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\FinalByte::SGR
        );
    }
}
