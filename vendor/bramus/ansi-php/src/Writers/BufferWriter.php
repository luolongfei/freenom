<?php

namespace Bramus\Ansi\Writers;

/**
 * Buffers data for another WriterInterface until asked to flush the buffer to it
 */
class BufferWriter implements WriterInterface, FlushableInterface
{
    /**
     * The buffer that holds the data
     * @var string
     */
    private $buffer = '';

    /**
     * Write Data
     * @param  string          $data Data to write
     * @return WriterInterface Self, for chaining
     */
    public function write($data)
    {
        // Add data to the buffer
        $this->buffer .= $data;

        // Afford chaining
        return $this;
    }

    /**
     * Get/Flush the data
     * @param  boolean $resetAfterwards Reset the data afterwards?
     * @return string  The data
     */
    public function flush($resetAfterwards = true)
    {
        // Get buffer contents
        $buffer = $this->buffer;

        // Clear buffer contents
        if ($resetAfterwards) {
            $this->clear();
        }

        // Return data that was flushed
        return $buffer;
    }

    /**
     * Reset/Clear the buffer
     * @return BufferedStreamWriter self, for chaining
     */
    public function clear()
    {
        // Clear the buffer
        $this->buffer = '';

        // Afford chaining
        return $this;
    }
}
