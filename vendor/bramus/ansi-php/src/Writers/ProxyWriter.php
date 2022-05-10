<?php

namespace Bramus\Ansi\Writers;

/**
 * Writer that acts as a proxy to write to another Writer
 */
class ProxyWriter extends BufferWriter
{
    /**
     * The writer to proxy for
     * @var WriterInterface
     */
    private $writer;

    /**
     * ProxyWriter — Writer that acts as a proxy to write to another Writer
     * @param WriterInterface $writer The writer to proxy for
     */
    public function __construct(WriterInterface $writer)
    {
        // Store writer
        $this->setWriter($writer);
    }

    /**
     * Set the writer to proxy for
     * @param WriterInterface $writer The writer to proxy for
     */
    public function setWriter(WriterInterface $writer)
    {
        $this->writer = $writer;
    }

    /**
     * Get the writer we are proxying for
     * @return WriterInterface The writer we are proxying for
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * Get/Flush the data
     * @param  boolean $resetAfterwards Reset the data afterwards?
     * @return string  The data
     */
    public function flush($resetAfterwards = true)
    {
        // Get the data from the buffer
        $data = parent::flush($resetAfterwards);

        // Write the data to the writer we are proxying for
        $this->writer->write($data);

        // Return the data
        return $data;
    }
}
