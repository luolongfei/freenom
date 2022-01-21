<?php
/**
 *
 */
namespace Bramus\Ansi\ControlFunctions;

class Tab extends Base
{
    public function __construct()
    {
        parent::__construct(Enums\C0::TAB);
    }
}
