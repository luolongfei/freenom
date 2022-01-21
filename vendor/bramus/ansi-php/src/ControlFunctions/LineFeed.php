<?php
/**
 *
 */
namespace Bramus\Ansi\ControlFunctions;

class LineFeed extends Base
{
    public function __construct()
    {
        parent::__construct(Enums\C0::LF);
    }
}
