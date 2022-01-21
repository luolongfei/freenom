<?php
/**
 *
 */
namespace Bramus\Ansi\ControlFunctions;

class CarriageReturn extends Base
{
    public function __construct()
    {
        parent::__construct(Enums\C0::CR);
    }
}
