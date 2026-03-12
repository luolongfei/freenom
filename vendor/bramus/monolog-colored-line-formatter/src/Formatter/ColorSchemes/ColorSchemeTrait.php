<?php

namespace Bramus\Monolog\Formatter\ColorSchemes;

use Bramus\Ansi\Ansi;
use Bramus\Ansi\Writers\BufferWriter;
use Exception;
use Monolog\Level;

trait ColorSchemeTrait
{
    /**
     * ANSI Wrapper which provides colors
     * @var Ansi
     */
    protected Ansi $ansi;

    /**
     * The Color Scheme Array
     * @var array
     */
    protected array $colorScheme = array();

    /*
     * Constructor
     */
    public function __construct()
    {
        // Create Ansi helper
        $this->ansi = new Ansi(new BufferWriter());
    }

    /**
     * Set the Color Scheme Array
     * @param array $colorScheme The Color Scheme Array
     */
    public function setColorizeArray(array $colorScheme): void
    {
        // Only store entries that exist as Monolog\Logger levels
        $colorScheme = array_intersect_key($colorScheme, array_combine(Level::VALUES, Level::NAMES));

        // Store the filtered colorScheme
        $this->colorScheme = $colorScheme;
    }

    /**
     * Get the Color Scheme Array
     * @return array The Color Scheme Array
     */
    public function getColorizeArray(): array
    {
        return $this->colorScheme;
    }

    /**
     * Get the Color Scheme String for the given Level
     * @param  int    $level The Logger Level
     * @return string The Color Scheme String
     */
    public function getColorizeString($level): string
    {
        return $this->colorScheme[$level] ?? '';
    }

    /**
     * Get the string identifier that closes/finishes the styling
     * @return string The reset string
     * @throws Exception
     */
    public function getResetString(): string
    {
        return $this->ansi->reset()->get();
    }
}
