<?php

namespace Bramus\Ansi\ControlSequences\Traits;

trait HasFinalByte
{
    /**
     * Final Byte: The bit combination that terminates an escape sequence or a control sequence.
     * @var string
     */
    protected $finalByte;

    /**
     * Set the finalByte
     * @param  string $finalByte The bit combination that terminates an escape sequence or a control sequence.
     * @return Base   self, for chaining
     */
    public function setFinalByte($finalByte)
    {
        // @TODO Verify Validity
        $this->finalByte = $finalByte;

        return $this;
    }

    /**
     * Get the Final Byte
     * @return string
     */
    public function getFinalByte()
    {
        return $this->finalByte;
    }
}
