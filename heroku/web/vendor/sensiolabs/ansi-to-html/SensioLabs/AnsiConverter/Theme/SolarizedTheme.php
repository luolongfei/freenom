<?php

/*
 * This file is part of ansi-to-html.
 *
 * (c) 2013 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SensioLabs\AnsiConverter\Theme;

/**
 * Solarized theme.
 *
 * @see http://ethanschoonover.com/solarized
 */
class SolarizedTheme extends Theme
{
    public function asArray()
    {
        return array(
            // normal
            'black' => '#073642',
            'red' => '#dc322f',
            'green' => '#859900',
            'yellow' => '#b58900',
            'blue' => '#268bd2',
            'magenta' => '#d33682',
            'cyan' => '#2aa198',
            'white' => '#eee8d5',

            // bright
            'brblack' => '#002b36',
            'brred' => '#cb4b16',
            'brgreen' => '#586e75',
            'bryellow' => '#657b83',
            'brblue' => '#839496',
            'brmagenta' => '#6c71c4',
            'brcyan' => '#93a1a1',
            'brwhite' => '#fdf6e3',
        );
    }
}
