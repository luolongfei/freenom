<?php

namespace Bramus\Ansi\Writers;

/**
 * Writes data to a stream
 */
class StreamWriter implements WriterInterface
{
    /**
     * Stream to write to
     * @var resource
     */
    private $stream;

    /**
     * Constructor
     * @param mixed $stream Stream to write to (Default: php://stdout)
     */
    public function __construct($stream = 'php://stdout')
    {
        $this->setStream($stream);
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        @fclose($this->stream);
    }

    /**
     * Set the stream to write to
     * @param mixed $stream Stream to write to
     */
    public function setStream($stream = null)
    {
        // String passed in? Try converting it to a stream
        if (is_string($stream)) {
            $stream = @fopen($stream, 'a');
        }

        // Make sure the stream is a resource
        if (!is_resource($stream)) {
            throw new \InvalidArgumentException('Invalid Stream');
        }

        // Store it
        $this->stream = $stream;

        // Afford chaining
        return $this;
    }

    /**
     * Get stream we are writing to
     * @return resource Stream we are writing to
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Write Data
     * @param  string          $data Data to write
     * @return WriterInterface Self, for chaining
     */
    public function write($data)
    {
        // Write data on the stream
        fwrite($this->stream, $data);

        // Afford chaining
        return $this;
    }
}
