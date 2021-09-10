<?php
/**
 * Possible Final Byte Values for Escape Sequences
 */
namespace Bramus\Ansi\ControlSequences\EscapeSequences\Enums;

class FinalByte
{
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
    // @see http://www.ecma-international.org/publications/files/ECMA-ST/Ecma-048.pdf
    // @see http://www.termsys.demon.co.uk/vtansi.htm
    // @see http://en.wikipedia.org/wiki/ANSI_escape_code
}
