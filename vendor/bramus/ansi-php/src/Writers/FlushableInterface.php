<?php

namespace Bramus\Ansi\Writers;

interface FlushableInterface
{
    /**
     * Get/Flush the data
     * @param  boolean $resetAfterwards Reset the data afterwards?
     * @return string  The data
     */
    public function flush($resetAfterwards = true);

    /**
     * Reset/Clear the buffer
     * @return BufferedStreamWriter self, for chaining
     */
    public function clear();
}
