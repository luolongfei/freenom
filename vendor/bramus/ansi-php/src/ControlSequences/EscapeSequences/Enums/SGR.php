<?php
/**
 * Possible Parameter Byte Values for SGR
 */
namespace Bramus\Ansi\ControlSequences\EscapeSequences\Enums;

class SGR
{
    /**
     * Default rendition, cancels the effect of any preceding occurrence of SGR in the data stream
     * @type string
     */
    const STYLE_NONE = '0';

    /**
     * Bold: On ~or~ increased intensity
     * @type string
     */
    const STYLE_INTENSITY_BRIGHT = '1';

    /**
     * Bold: On ~or~ increased intensity
     * @type string
     */
    const STYLE_BOLD = '1';

    /**
     * Faint, decreased intensity
     * @note Not widely supported
     * @type string
     */
    const STYLE_INTENSITY_FAINT = '2';

    /**
     * Italic: On
     * @note Not widely supported
     * @type string
     */
    const STYLE_ITALIC = '3';

    /**
     * Underline: On
     * @type string
     */
    const STYLE_UNDERLINE = '4';

    /**
     * Blink: On
     * @type string
     */
    const STYLE_BLINK = '5';

    /**
     * Blink (Rapid): On
     * @note Not widely supported
     * @type string
     */
    const STYLE_BLINK_RAPID = '6';

    /**
     * Inverse or reverse colors (viz. swap foreground and background)
     * @type string
     */
    const STYLE_NEGATIVE = '7';

    /**
     * Conceal (Hide) text
     * @note Not widely supported
     * @type string
     */
    const STYLE_CONCEAL = '8';

    /**
     * Cross-out / strikethrough: On
     * @note Not widely supported
     * @type string
     */
    const STYLE_STRIKETHROUGH = '9';

    /**
     * Bold: Off ~or~ normal intensity
     * @type string
     */
    const STYLE_INTENSITY_NORMAL = '22';

    /**
     * Bold: Off ~or~ normal intensity
     * @type string
     */
    const STYLE_BOLD_OFF = '22';

    /**
     * Italic: Off
     * @note Not widely supported
     * @type string
     */
    const STYLE_ITALIC_OFF = '23';

    /**
     * Underline: Off
     * @type string
     */
    const STYLE_UNDERLINE_OFF = '24';

    /**
     * Blink: Off (Steady)
     * @type string
     */
    const STYLE_STEADY = '5';
    const STYLE_BLINK_OFF = '5';

    /**
     * Positive Image (viz. don't swap foreground and background)
     * @type string
     */
    const STYLE_POSITIVE = '27';

    /**
     * Revealed Charcters (inverse of CONCEAL)
     * @type string
     */
    const STYLE_REVEAL = '28';

    /**
     * Strikethrough: Off
     * @note Not widely supported
     * @type string
     */
    const STYLE_STRIKETHROUGH_OFF = '29';

    /**
     * Black Foreground Color
     * @type string
     */
    const COLOR_FG_BLACK = '30';

    /**
     * Red Foreground Color
     * @type string
     */
    const COLOR_FG_RED = '31';

    /**
     * Green Foreground Color
     * @type string
     */
    const COLOR_FG_GREEN = '32';

    /**
     * Yellow Foreground Color
     * @type string
     */
    const COLOR_FG_YELLOW = '33';

    /**
     * Blue Foreground Color
     * @type string
     */
    const COLOR_FG_BLUE = '34';

    /**
     * Purple Foreground Color
     * @type string
     */
    const COLOR_FG_PURPLE = '35';

    /**
     * Cyan Foreground Color
     * @type string
     */
    const COLOR_FG_CYAN = '36';

    /**
     * White Foreground Color
     * @type string
     */
    const COLOR_FG_WHITE = '37';

    /**
     * Default Foreground Color
     * @type string
     */
    const COLOR_FG_RESET = '39';

    /**
     * Black Background Color
     * @type string
     */
    const COLOR_BG_BLACK = '40';

    /**
     * Red Background Color
     * @type string
     */
    const COLOR_BG_RED = '41';

    /**
     * Green Background Color
     * @type string
     */
    const COLOR_BG_GREEN = '42';

    /**
     * Yellow Background Color
     * @type string
     */
    const COLOR_BG_YELLOW = '43';

    /**
     * Blue Background Color
     * @type string
     */
    const COLOR_BG_BLUE = '44';

    /**
     * Purple Background Color
     * @type string
     */
    const COLOR_BG_PURPLE = '45';

    /**
     * Cyan Background Color
     * @type string
     */
    const COLOR_BG_CYAN = '46';

    /**
     * White Background Color
     * @type string
     */
    const COLOR_BG_WHITE = '47';

    /**
     * Default Background Color
     * @type string
     */
    const COLOR_BG_RESET = '49';

    /**
     * Framed: On
     * @note Not widely supported
     * @type string
     */
    const STYLE_FRAMED = '51';

    /**
     * Encircled
     * @note Not widely supported
     * @type string
     */
    const STYLE_ENCIRCLED = '52';

    /**
     * Overlined: On
     * @note Not widely supported
     * @type string
     */
    const STYLE_OVERLINED = '53';

    /**
     * Framed: Off
     * @note Not widely supported
     * @type string
     */
    const STYLE_FRAMED_ENCIRCLED_OFF = '54';

    /**
     * Overlined: Off
     * @note Not widely supported
     * @type string
     */
    const STYLE_OVERLINED_OFF = '55';

    /**
     * Black Foreground Color (High Intensity)
     * @note Not part of the ANSI standard
     * @type string
     */
    const COLOR_FG_BLACK_BRIGHT = '90';

    /**
     * Red Foreground Color (High Intensity)
     * @note Not part of the ANSI standard
     * @type string
     */
    const COLOR_FG_RED_BRIGHT = '91';

    /**
     * Green Foreground Color (High Intensity)
     * @note Not part of the ANSI standard
     * @type string
     */
    const COLOR_FG_GREEN_BRIGHT = '92';

    /**
     * Yellow Foreground Color (High Intensity)
     * @note Not part of the ANSI standard
     * @type string
     */
    const COLOR_FG_YELLOW_BRIGHT = '93';

    /**
     * Blue Foreground Color (High Intensity)
     * @note Not part of the ANSI standard
     * @type string
     */
    const COLOR_FG_BLUE_BRIGHT = '94';

    /**
     * Purple Foreground Color (High Intensity)
     * @note Not part of the ANSI standard
     * @type string
     */
    const COLOR_FG_PURPLE_BRIGHT = '95';

    /**
     * Bright Foreground Color (High Intensity)
     * @note Not part of the ANSI standard
     * @type string
     */
    const COLOR_FG_CYAN_BRIGHT = '96';

    /**
     * White Foreground Color (High Intensity)
     * @note Not part of the ANSI standard
     * @type string
     */
    const COLOR_FG_WHITE_BRIGHT = '97';

    /**
     * Black Background Color (High Intensity)
     * @note Not part of the ANSI standard
     * @type string
     */
    const COLOR_BG_BLACK_BRIGHT = '100';

    /**
     * Red Background Color (High Intensity)
     * @note Not part of the ANSI standard
     * @type string
     */
    const COLOR_BG_RED_BRIGHT = '101';

    /**
     * Green Background Color (High Intensity)
     * @note Not part of the ANSI standard
     * @type string
     */
    const COLOR_BG_GREEN_BRIGHT = '102';

    /**
     * Yellow Background Color (High Intensity)
     * @note Not part of the ANSI standard
     * @type string
     */
    const COLOR_BG_YELLOW_BRIGHT = '103';

    /**
     * Blue Background Color (High Intensity)
     * @note Not part of the ANSI standard
     * @type string
     */
    const COLOR_BG_BLUE_BRIGHT = '104';

    /**
     * Purple Background Color (High Intensity)
     * @note Not part of the ANSI standard
     * @type string
     */
    const COLOR_BG_PURPLE_BRIGHT = '105';

    /**
     * Cyan Background Color (High Intensity)
     * @note Not part of the ANSI standard
     * @type string
     */
    const COLOR_BG_CYAN_BRIGHT = '106';

    /**
     * White Background Color (High Intensity)
     * @note Not part of the ANSI standard
     * @type string
     */
    const COLOR_BG_WHITE_BRIGHT = '107';
}
