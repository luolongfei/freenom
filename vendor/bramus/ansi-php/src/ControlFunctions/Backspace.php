<?php
/**
 *
 */
namespace Bramus\Ansi\ControlFunctions;

class Backspace extends Base
{
    public function __construct()
    {
        parent::__construct(Enums\C0::BACKSPACE);
    }
}
