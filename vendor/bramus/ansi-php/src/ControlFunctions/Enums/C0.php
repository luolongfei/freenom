<?php
/**
 * The ANSI C0 Set
 *
 * Based upon http://www.ecma-international.org/publications/files/ECMA-ST/Ecma-048.pdf, section 5.2
 *
 */
namespace Bramus\Ansi\ControlFunctions\Enums;

class C0
{
    /**
     * NULL
     *
     * NUL is used for media-fill or time-fill. NUL characters may be inserted
     * into, or removed from, a data stream without affecting the information
     * content of that stream, but such action may affect the information layout
     * and/or the control of equipment.
     *
     * @var String
     */
    const NUL = "\000";

    /**
     * START OF HEADING
     *
     * SOH is used to indicate the beginning of a heading.
     * The use of SOH is defined in ISO 1745.
     *
     * @var String
     */
    const SOH = "\001";

    /**
     * START OF TEXT
     *
     * STX is used to indicate the beginning of a text and the end of a heading.
     * The use of STX is defined in ISO 1745.
     *
     * @var String
     */
    const STX = "\002";

    /**
     * END OF TEXT
     *
     * ETX is used to indicate the end of a text.
     * The use of ETX is defined in ISO 1745.
     *
     * @var String
     */
    const ETX = "\003";

    /**
     * END OF TRANSMISSION
     *
     * EOT is used to indicate the conclusion of the transmission of one or more texts.
     * The use of EOT is defined in ISO 1745.
     *
     * @var String
     */
    const EOT = "\004";

    /**
     * ENQUIRY
     *
     * ENQ is transmitted by a sender as a request for a response from a receiver.
     * The use of ENQ is defined in ISO 1745.
     *
     * @var String
     */
    const ENQ = "\005";

    /**
     * ACKNOWLEDGE
     *
     * ACK is transmitted by a receiver as an affirmative response to the sender.
     * The use of ACK is defined in ISO 1745.
     *
     * @var String
     */
    const ACK = "\006";

    /**
     * BELL
     *
     * BEL is used when there is a need to call for attention; it may control alarm
     * or attention devices.
     *
     * @var string
     */
    const BEL = "\007";
    const BELL = "\007";

    /**
     * BACKSPACE
     *
     * BS causes the active data position to be moved one character position in the
     * data component in the direction opposite to that of the implicit movement.
     *
     * @var string
     */
    const BS = "\010";
    const BACKSPACE = "\010";

    /**
     * CHARACTER TABULATION (HORIZONTAL TAB)
     *
     * HT causes the active presentation position to be moved to the following
     * character tabulation stop in the presentation component.
     *
     * @var string
     */
    const HT = "\011";
    const TAB = "\011";

    /**
     * LINE FEED
     *
     * LF causes the active data position to be moved to the corresponding
     * character position of the following line in the data component.
     *
     * @var string
     */
    const LF = "\012";

    /**
     * LINE TABULATION (VERTICAL TAB)
     *
     * VT causes the active presentation position to be moved in the presentation
     * component to the corresponding character position on the line at which the
     * following line tabulation stop is set.
     *
     * @var String
     */
    const VT = "\013";

    /**
     * FORM FEED
     *
     * FF causes the active presentation position to be moved to the corresponding
     * character position of the line at the page home position of the next form or
     * page in the presentation component
     *
     * @var String
     */
    const FF = "\014";

    /**
     * CARRIAGE RETURN
     *
     * CR causes the active data position to be moved to the line home position of
     * the same line in the data component
     *
     * @var string
     */
    const CR = "\015";

    /**
     * LOCKING-SHIFT ONE
     *
     * LS1 is used for code extension purposes. It causes the meanings of the bit
     * combinations following it in the data stream to be changed.
     * The use of LS1 is defined in Standard ECMA-35.
     *
     * @var String
     */
    const LS1 = "\016";

    /**
     * LOCKING-SHIFT ZERO
     *
     * LS0 is used for code extension purposes. It causes the meanings of the bit
     * combinations following it in the data stream to be changed.
     * The use of LS0 is defined in Standard ECMA-35.
     *
     * @var String
     */
    const LS0 = "\017";

    /**
     * DATA LINK ESCAPE
     *
     * DLE is used exclusively to provide supplementary transmission control functions.
     * The use of DLE is defined in ISO 1745.
     *
     * @var String
     */
    const DLE = "\020";

    /**
     * DEVICE CONTROL ONE
     *
     * DC1 is primarily intended for turning on or starting an ancillary device.
     * If it is not required for this purpose, it may be used to restore a device to
     * the basic mode of operation (see also DC2 and DC3), or any other device control
     * function not provided by other DCs.
     *
     * @var String
     */
    const DC1 = "\021";

    /**
     * DEVICE CONTROL TWO
     *
     * DC2 is primarily intended for turning on or starting an ancillary device.
     * If it is not required for this purpose, it may be used to set a device to a
     * special mode of operation (in which case DC1 is used to restore the device to
     * the basic mode), or for any other device control function not provided
     * by other DCs.
     *
     * @var String
     */
    const DC2 = "\022";

    /**
     * DEVICE CONTROL THREE
     *
     * DC3 is primarily intended for turning off or stopping an ancillary device.
     * This function may be a secondary level stop, for example wait, pause,
     * stand-by or halt (in which case DC1 is used to restore normal operation).
     * If it is not required for this purpose, it may be used for any other device control
     * function not provided by other DCs.
     *
     * @var String
     */
    const DC3 = "\023";

    /**
     * DEVICE CONTROL FOUR
     *
     * DC4 is primarily intended for turning off, stopping or interrupting an ancillary
     * device. If it is not required for this purpose, it may be used for any other device
     * control function not provided by other DCs
     *
     * @var String
     */
    const DC4 = "\024";

    /**
     * NEGATIVE ACKNOWLEDGE
     *
     * NAK is transmitted by a receiver as a negative response to the sender.
     * The use of NAK is defined in ISO 1745.
     *
     * @var String
     */
    const NAK = "\025";

    /**
     * SYNCHRONOUS IDLE
     *
     * SYN is used by a synchronous transmission system in the absence of any other
     * character (idle condition) to provide a signal from which synchronism may be
     * achieved or retained between data terminal equipment.
     * The use of SYN is defined in ISO 1745.
     *
     * @var String
     */
    const SYN = "\026";

    /**
     * END OF TRANSMISSION BLOCK
     *
     * ETB is used to indicate the end of a block of data where the data are
     * divided into such blocks for transmission purposes.
     * The use of ETB is defined in ISO 1745.
     *
     * @var String
     */
    const ETB = "\027";

    /**
     * CANCEL
     *
     * CAN is used to indicate that the data preceding it in the data stream is
     * in error. As a result, this data shall be ignored. The specific meaning
     * of this control function shall be defined for each application and/or
     * between sender and recipient.
     *
     * @var String
     */
    const CAN = "\030";
    const CANCEL = "\030";

    /**
     * END OF MEDIUM
     *
     * EM is used to identify the physical end of a medium, or the end of the used
     * portion of a medium, or the end of the wanted portion of data recorded on
     * a medium.
     *
     * @var String
     */
    const EM = "\031";

    /**
     * SUBSTITUTE
     *
     * SUB is used in the place of a character that has been found to be invalid
     * or in error. SUB is intended to be introduced by automatic means.
     *
     * @var String
     */
    const SUB = "\032";

    /**
     * ESCAPE
     *
     * ESC is used for code extension purposes. It causes the meanings of a limited
     * number of bit combinations following it in the data stream to be changed.
     * The use of ESC is defined in Standard ECMA-35.
     *
     * @var string
     */
    const ESC = "\033";

    /**
     * INFORMATION SEPARATOR FOUR (FS - FILE SEPARATOR)
     *
     * IS4 is used to separate and qualify data logically; its specific meaning has
     * to be defined for each application. If this control function is used in
     * hierarchical order, it may delimit a data item called a file.
     *
     * @var String
     */
    const IS4 = "\034";

    /**
     * INFORMATION SEPARATOR THREE (GS - GROUP SEPARATOR)
     *
     * IS3 is used to separate and qualify data logically; its specific meaning has
     * to be defined for each application. If this control function is used in
     * hierarchical order, it may delimit a data item called a group.
     *
     * @var String
     */
    const IS3 = "\035";

    /**
     * INFORMATION SEPARATOR TWO (RS - RECORD SEPARATOR)
     *
     * IS2 is used to separate and qualify data logically; its specific meaning has
     * to be defined for each application. If this control function is used in
     * hierarchical order, it may delimit a data item called a record.
     *
     * @var String
     */
    const IS2 = "\036";

    /**
     * INFORMATION SEPARATOR ONE (US - UNIT SEPARATOR)
     *
     * IS1 is used to separate and qualify data logically; its specific meaning has
     * to be defined for each application. If this control function is used in
     * hierarchical order, it may delimit a data item called a unit.
     *
     * @var String
     */
    const IS1 = "\037";
}
