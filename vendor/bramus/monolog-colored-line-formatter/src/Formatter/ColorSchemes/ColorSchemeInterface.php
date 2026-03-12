<?php

namespace Bramus\Monolog\Formatter\ColorSchemes;

interface ColorSchemeInterface
{
    /**
     * Set the Color Scheme
     * @param array $colorScheme The Color Scheme
     */
    public function setColorizeArray(array $colorScheme): void;

    /**
     * Get the Color Scheme
     * @return array Color Scheme
     */
    public function getColorizeArray(): array;

    /**
     * Get the Color Scheme String for the given Level
     * @param  int    $level The Logger Level
     * @return string The Color Scheme String
     */
    public function getColorizeString($level): string;

    /**
     * Get the string identifier that closes/finishes the styling
     * @return string The reset code
     */
    public function getResetString(): string;
}
