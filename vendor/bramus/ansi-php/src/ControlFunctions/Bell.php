<?php
/**
 *
 */
namespace Bramus\Ansi\ControlFunctions;

class Bell extends Base
{
    public function __construct()
    {
        parent::__construct(Enums\C0::BELL);
    }
}
