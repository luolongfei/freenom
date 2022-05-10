<?php
/**
 * Possible Final Byte Values for Escape Sequences
 */
namespace Bramus\Ansi\ControlSequences\EscapeSequences\Enums;

class FinalByte
{

    /**
     * CUB - CURSOR BACK
     * 
     * CUB causes the active presentation position to be moved leftwards
     * in the presentation component by n character positions if the
     * character path is horizontal, or by n line positions if the
     * character path is vertical, where n equals the value of Pn.
     * 
     * @type string
     */
    const CUB = 'D';

    /**
     * CUD - CURSOR DOWN
     * 
     * CUD causes the active presentation position to be moved downwards
     * in the presentation component by n line positions if the character
     * path is horizontal, or by n character positions if the character
     * path is vertical, where n equals the value of Pn.
     * 
     * @type string
     */
    const CUD = 'B';

    /**
     * CUF - CURSOR FORWARD
     * 
     * CUF causes the active presentation position to be moved rightwards
     * in the presentation component by n character positions if the
     * character path is horizontal, or by n line positions if the
     * character path is vertical, where n equals the value of Pn.
     * 
     * @type string
     */
    const CUF = 'C';

    /**
     * CUP - CURSOR POSITION
     * 
     * CUP causes the active presentation position to be moved in the
     * presentation component to the n-th line position according to the
     * line progression and to the m-th character position according to
     * the character path, where n equals the value of Pn1 and m equals
     * the value of Pn2.
     * 
     * @type string
     */
    const CUP = 'H';

    /**
     * CUU - CURSOR UP 
     *
     * CUU causes the active presentation position to be moved upwards in
     * the presentation component by n line positions if the character
     * path is horizontal, or by n character positions if the character
     * path is vertical, where n equals the value of Pn.
     * 
     * @type string
     */
    const CUU = 'A';

    /**
     * ED - ERASE DISPLAY (ERASE IN PAGE)
     *
     * ED causes some or all character positions of the active page
     * (the page which contains the active presentation position in the
     * presentation component) to be put into the erased state, depending
     * on the parameter values
     *
     * @type string
     */
    const ED = 'J';

    /**
     * EL - ERASE IN LINE
     *
     * EL causes some or all character positions of the active line (the
     * line which contains the active data position in the data component)
     * to be put into the erased state, depending on the parameter values
     *
     * @type string
     */
    const EL = 'K';

    /**
     * SGR - SELECT GRAPHIC RENDITION
     *
     * SGR is used to establish one or more graphic rendition aspects for
     * subsequent text. The established aspects remain in effect until the
     * next occurrence of SGR in the data stream, depending on the setting
     * of the GRAPHIC RENDITION COMBINATION MODE (GRCM). Each graphic
     * rendition aspect is specified by a parameter value
     *
     * @type string
     */
    const SGR = 'm';

    // @TODO: Add more Escape Code Final Bytes
    // @see https://www.ecma-international.org/publications-and-standards/standards/ecma-48/
    // @see https://www2.ccs.neu.edu/research/gpc/VonaUtils/vona/terminal/vtansi.htm
    // @see https://en.wikipedia.org/wiki/ANSI_escape_code
}
