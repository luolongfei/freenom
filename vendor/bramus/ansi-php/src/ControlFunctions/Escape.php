<?php
/**
 *
 */
namespace Bramus\Ansi\ControlFunctions;

class Escape extends Base
{
    public function __construct()
    {
        parent::__construct(Enums\C0::ESC);
    }
}
