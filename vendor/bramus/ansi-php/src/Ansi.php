<?php

namespace Bramus\Ansi;

/**
 * ANSI Wrapper Class to work with \Bramus\Ansi more easily
 */
class Ansi
{
    /**
     * Traits to use
     */
    use Traits\ControlFunctions;
    use Traits\EscapeSequences\ED;
    use Traits\EscapeSequences\EL;
    use Traits\EscapeSequences\CUB;
    use Traits\EscapeSequences\CUD;
    use Traits\EscapeSequences\CUF;
    use Traits\EscapeSequences\CUP;
    use Traits\EscapeSequences\CUU;
    use Traits\EscapeSequences\SGR;

    /**
     * The writer to write the data to
     * @var Writers\WriterInterface
     */
    protected $writer;

    /**
     * ANSI Wrapper Class to work with \Bramus\Ansi more easily
     * @param Writers\WriterInterface $writer writer to use
     */
    public function __construct($writer = null)
    {
        // Enforce having a writer
        if (!$writer) {
            $writer = new Writers\StreamWriter();
        }

        // Set the writer
        $this->setWriter($writer);
    }

    /**
     * Sets the writer
     * @param Writers\WriterInterface $writer The writer to use
     */
    public function setWriter(Writers\WriterInterface $writer)
    {
        $this->writer = $writer;
    }

    /**
     * Gets the writer
     * @return Writers\WriterInterface $writer The writer used
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * Write a piece of text onto the writer
     * @param  string $text The text to write
     * @return Ansi   self, for chaining
     */
    public function text($text)
    {
        // Write the text to the writer
        $this->writer->write($text);

        // Afford chaining
        return $this;
    }

    /**
     * Flush the contents of the writer
     * @param  $resetAfterwards Reset the writer contents after flushing?
     * @return string The writer contents
     */
    public function flush($resetAfterwards = true)
    {
        if ($this->writer instanceof Writers\FlushableInterface) {
            return $this->writer->flush($resetAfterwards);
        } else {
            throw new \Exception('Flushing a non FlushableInterface is not possible');
        }
    }

    public function get($resetAfterwards = true)
    {
        try {
            return $this->flush($resetAfterwards);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Echo the contents of the writer
     * @param  $resetAfterwards Reset the writer contents after flushing?
     * @return Ansi self, for chaining
     */
    public function e($resetAfterwards = true)
    {
        try {
            // Get the contents and echo them
            echo $this->flush($resetAfterwards);

            // Afford chaining
            return $this;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
