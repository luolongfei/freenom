<?php

namespace Colors;

class Color
{
    const FORMAT_PATTERN = '#<([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)>(.*?)</\\1?>#s';
    /** @link http://www.php.net/manual/en/functions.user-defined.php */
    const STYLE_NAME_PATTERN = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/';

    const ESC = "\033[";
    const ESC_SEQ_PATTERN = "\033[%sm";

    protected $initial = '';
    protected $wrapped = '';
    // italic and blink may not work depending of your terminal
    protected $styles = array(
        'reset'            => '0',
        'bold'             => '1',
        'dark'             => '2',
        'italic'           => '3',
        'underline'        => '4',
        'blink'            => '5',
        'reverse'          => '7',
        'concealed'        => '8',

        'default'          => '39',
        'black'            => '30',
        'red'              => '31',
        'green'            => '32',
        'yellow'           => '33',
        'blue'             => '34',
        'magenta'          => '35',
        'cyan'             => '36',
        'light_gray'       => '37',

        'dark_gray'        => '90',
        'light_red'        => '91',
        'light_green'      => '92',
        'light_yellow'     => '93',
        'light_blue'       => '94',
        'light_magenta'    => '95',
        'light_cyan'       => '96',
        'white'            => '97',

        'bg_default'       => '49',
        'bg_black'         => '40',
        'bg_red'           => '41',
        'bg_green'         => '42',
        'bg_yellow'        => '43',
        'bg_blue'          => '44',
        'bg_magenta'       => '45',
        'bg_cyan'          => '46',
        'bg_light_gray'    => '47',

        'bg_dark_gray'     => '100',
        'bg_light_red'     => '101',
        'bg_light_green'   => '102',
        'bg_light_yellow'  => '103',
        'bg_light_blue'    => '104',
        'bg_light_magenta' => '105',
        'bg_light_cyan'    => '106',
        'bg_white'         => '107',
    );
    protected $userStyles = array();
    protected $isStyleForced = false;

    public function __construct($string = '')
    {
        $this->setInternalState($string);
    }

    public function __invoke($string)
    {
        return $this->setInternalState($string);
    }

    public function __call($method, $args)
    {
        if (count($args) >= 1) {
            return $this->apply($method, $args[0]);
        }

        return $this->apply($method);
    }

    public function __get($name)
    {
        return $this->apply($name);
    }

    public function __toString()
    {
        return $this->wrapped;
    }

    public function setForceStyle($force)
    {
        $this->isStyleForced = (bool) $force;
    }

    public function isStyleForced()
    {
        return $this->isStyleForced;
    }

    /**
     * Returns true if the stream supports colorization.
     *
     * Colorization is disabled if not supported by the stream:
     *
     *  -  Windows without Ansicon, ConEmu or Babun
     *  -  non tty consoles
     *
     * @return bool true if the stream supports colorization, false otherwise
     *
     * @link https://github.com/symfony/Console/blob/master/Output/StreamOutput.php#L94
     * @codeCoverageIgnore
     */
    public function isSupported()
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            return (function_exists('sapi_windows_vt100_support')
                && @sapi_windows_vt100_support(STDOUT))
                || false !== getenv('ANSICON')
                || 'ON' === getenv('ConEmuANSI')
                || 'xterm' === getenv('TERM');
        }

        if (function_exists('stream_isatty')) {
            return @stream_isatty(STDOUT);
        }

        if (function_exists('posix_isatty')) {
            return @posix_isatty(STDOUT);
        }

        $stat = @fstat($this->stream);
        // Check if formatted mode is S_IFCHR
        return $stat ? 0020000 === ($stat['mode'] & 0170000) : false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function are256ColorsSupported()
    {
        return DIRECTORY_SEPARATOR === '/' && false !== strpos(getenv('TERM'), '256color');
    }

    protected function setInternalState($string)
    {
        $this->initial = $this->wrapped = (string) $string;
        return $this;
    }

    protected function stylize($style, $text)
    {
        if (!$this->shouldStylize()) {
            return $text;
        }

        $style = strtolower($style);

        if ($this->isUserStyleExists($style)) {
            return $this->applyUserStyle($style, $text);
        }

        if ($this->isStyleExists($style)) {
            return $this->applyStyle($style, $text);
        }

        if (preg_match('/^((?:bg_)?)color\[([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\]$/', $style, $matches)) {
            $option = $matches[1] == 'bg_' ? 48 : 38;
            return $this->buildEscSeq("{$option};5;{$matches[2]}") . $text . $this->buildEscSeq($this->styles['reset']);
        }

        throw new NoStyleFoundException("Invalid style $style");
    }

    protected function shouldStylize()
    {
        return $this->isStyleForced() || $this->isSupported();
    }

    protected function isStyleExists($style)
    {
        return array_key_exists($style, $this->styles);
    }

    protected function applyStyle($style, $text)
    {
        return $this->buildEscSeq($this->styles[$style]) . $text . $this->buildEscSeq($this->styles['reset']);
    }

    protected function buildEscSeq($style)
    {
        return sprintf(self::ESC_SEQ_PATTERN, $style);
    }

    protected function isUserStyleExists($style)
    {
        return array_key_exists($style, $this->userStyles);
    }

    protected function applyUserStyle($userStyle, $text)
    {
        $styles = (array) $this->userStyles[$userStyle];

        foreach ($styles as $style) {
            $text = $this->stylize($style, $text);
        }

        return $text;
    }

    public function apply($style, $text = null)
    {
        if ($text === null) {
            $this->wrapped = $this->stylize($style, $this->wrapped);
            return $this;
        }

        return $this->stylize($style, $text);
    }

    public function fg($color, $text = null)
    {
        return $this->apply($color, $text);
    }

    public function bg($color, $text = null)
    {
        return $this->apply('bg_' . $color, $text);
    }

    public function highlight($color, $text = null)
    {
        return $this->bg($color, $text);
    }

    public function reset()
    {
        $this->wrapped = $this->initial;
        return $this;
    }

    public function center($width = 80, $text = null)
    {
        if ($text === null) {
            $text = $this->wrapped;
        }

        $centered = '';
        foreach (explode(PHP_EOL, $text) as $line) {
            $line = trim($line);
            $lineWidth = strlen($line) - mb_strlen($line, 'UTF-8') + $width;
            $centered .= str_pad($line, $lineWidth, ' ', STR_PAD_BOTH) . PHP_EOL;
        }

        $this->setInternalState(trim($centered, PHP_EOL));
        return $this;
    }

    protected function stripColors($text)
    {
        return preg_replace('/' . preg_quote(self::ESC) . '\d+m/', '', $text);
    }

    public function clean($text = null)
    {
        if ($text === null) {
            $this->wrapped = $this->stripColors($this->wrapped);
            return $this;
        }

        return $this->stripColors($text);
    }

    public function strip($text = null)
    {
        return $this->clean($text);
    }

    public function isAValidStyleName($name)
    {
        return preg_match(self::STYLE_NAME_PATTERN, $name);
    }

    /**
     * @deprecated
     * @codeCoverageIgnore
     */
    public function setTheme(array $theme)
    {
        return $this->setUserStyles($theme);
    }

    public function setUserStyles(array $userStyles)
    {
        foreach ($userStyles as $name => $styles) {
            if (!$this->isAValidStyleName($name)) {
                throw new InvalidStyleNameException("$name is not a valid style name");
            }

            if (in_array($name, (array) $styles)) {
                throw new RecursionInUserStylesException('User style cannot reference itself.');
            }
        }

        $this->userStyles = $userStyles;
        return $this;
    }

    protected function colorizeText($text)
    {
        return preg_replace_callback(self::FORMAT_PATTERN, array($this, 'replaceStyle'), $text);
    }

    public function colorize($text = null)
    {
        if ($text === null) {
            $this->wrapped = $this->colorizeText($this->wrapped);
            return $this;
        }

        return $this->colorizeText($text);
    }

    protected function replaceStyle($matches)
    {
        return $this->apply($matches[1], $this->colorize($matches[2]));
    }
}
