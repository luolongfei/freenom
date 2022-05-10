<?php

namespace Bramus\Ansi\ControlSequences\Traits;

trait HasIntermediateBytes
{
    /**
     * Intermediate Byte: In a control sequence, a bit combination that may occur between the ControlFunction CSI and the Final Byte, or between a Parameter Byte and the Final Byte.
     * @var array
     */
    protected $intermediateBytes = array();

    /**
     * Add a Intermediate Byte
     * @param  string $intermediateByte The byte to add
     * @return Base   self, for chaining
     */
    public function addIntermediateByte($intermediateByte)
    {
        $this->intermediateBytes[] = (string) $intermediateByte;

        return $this;
    }

    /**
     * Set the Intermediate Byte
     * @param  array $parameterByte The byte to add
     * @return Base  self, for chaining
     */
    public function setIntermediateBytes($intermediateBytes)
    {
        foreach ((array) $intermediateBytes as $byte) {
            $this->addIntermediateByte($byte);
        }

        return $this;
    }

    /**
     * Get the Intermediate Byte
     * @param  bool $asString As a string, or as an array?
     * @return Base self, for chaining
     */
    public function getIntermediateBytes($asString = true)
    {
        if ($asString === true) {
            return implode($this->intermediateBytes);
        } else {
            return $this->intermediateBytes;
        }
    }
}
