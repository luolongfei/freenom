<?php

namespace Bramus\Ansi\Writers;

/**
 * Writer Interface
 */
interface WriterInterface
{
    /**
     * Write Data
     * @param  string          $data Data to write
     * @return WriterInterface Self, for chaining
     */
    public function write($data);
}
